<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\BuyTicketRequest;
use App\Http\Requests\Ticket\CheckTicketRequest;
use App\Http\Resources\Ticket\AllBoughtTicketResource;
use App\Http\Resources\Ticket\SingleBoughtTicketResource;
use App\Models\Attendee;
use App\Models\BuyTicket;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class BuyTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user, Event $event)
    {
        Gate::authorize('viewAny', $user);

        $perPage = request()->query('per_page', 15);
        return AllBoughtTicketResource::collection($user->buyTickets()->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuyTicketRequest $request, Event $event)
    {
        $ticketsData = $request->validated()['tickets'];
        $paymentStatus = $request->validated();
        $allTickets = [];

        if (request()->user()->id === $event->user_id) {
            return response()->json([
                'error' => 'User cannot buy their own ticket'
            ], 409);
        } else {
            try {
                // First, validate that all ticket IDs belong to this event
                foreach ($ticketsData as $ticketData) {
                    $ticket = Ticket::where('id', $ticketData['ticket_id'])
                        ->where('event_id', $event->id)
                        ->first();

                    if (!$ticket) {
                        return response()->json([
                            'error' => 'Ticket ID ' . $ticketData['ticket_id'] . ' does not belong to this event'
                        ], 400);
                    }
                }

                DB::transaction(function () use ($ticketsData, $event, &$allTickets, $paymentStatus) {
                    foreach ($ticketsData as $ticketData) {
                        $ticket = Ticket::findOrFail($ticketData['ticket_id']);
                        $requestedQty = $ticketData['qty'];

                        if ($ticket->available_qty < $requestedQty) {
                            throw new Exception("Insufficient quantity for ticket type: " . $ticket->ticket_type);
                        }

                        if ($paymentStatus['payment_status'] == false) {
                            throw new Exception("Must be paying first to get your ticket");
                        }

                        $ticket->available_qty -= $requestedQty;
                        $ticket->sold_qty += $requestedQty;
                        $ticket->save();

                        for ($i = 0; $i < $requestedQty; $i++) {
                            do {
                                $ticketCode = strtoupper(Str::random(15));
                            } while (BuyTicket::where('ticket_code', $ticketCode)->exists());

                            $singleTicket = BuyTicket::create([
                                'event_id' => $event->id,
                                'ticket_id' => $ticket->id,
                                'user_id' => request()->user()->id,
                                'ticket_code' => $ticketCode,
                                'price' => $ticket->price,
                                'payment_status' => $paymentStatus['payment_status'],
                            ]);

                            $allTickets[] = $singleTicket;

                            Attendee::create([
                                'event_id' => $event->id,
                                'buy_ticket_id' => $singleTicket->id,
                                'user_id' => request()->user()->id,
                            ]);
                        }
                    }
                });

                return AllBoughtTicketResource::collection($allTickets);

            } catch (Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 400);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, BuyTicket $buyTicket)
    {
        Gate::authorize('view', $buyTicket);
        return SingleBoughtTicketResource::make($buyTicket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CheckTicketRequest $request, User $user)
    {
        if (auth()->id() !== $user->id) {
            return response()->json([
                'error' => 'Unauthorized: You are not the correct user.'
            ], 403);
        }

        $validatedData = $request->validated();
        $ticketCodes = $validatedData['tickets'];
        $successCount = 0;
        $now = now();
        $scannedTicketTypes = [];
        $errorMessage = null;

        foreach ($ticketCodes as $ticketCode) {
            // First find the ticket in BuyTickets based on code
            $buyTicket = BuyTicket::where('ticket_code', $ticketCode)->first();

            if (!$buyTicket) {
                $errorMessage = 'Ticket not found';
                continue;
            }

            // Get event and ticket details
            $event = Event::find($buyTicket->event_id);
            $ticket = Ticket::find($buyTicket->ticket_id);

            if (!$event || !$ticket) {
                $errorMessage = 'Invalid event or ticket type';
                continue;
            }

            // Check if user has access to this event
            $userHasAccess = $user->events()->where('id', $event->id)->exists();
            if (!$userHasAccess) {
                $errorMessage = 'Unauthorized: You do not have access to this event';
                continue;
            }

            // Check if the event is currently ongoing
            $eventStartTime = Carbon::parse($event->start_time);
            $eventEndTime = Carbon::parse($event->end_time);

            if ($now->lt($eventStartTime)) {
                $errorMessage = 'Cannot scan: Event has not started yet';
                continue;
            }

            if ($now->gt($eventEndTime)) {
                $errorMessage = 'Cannot scan: Event has already ended';
                continue;
            }

            // Delete/mark as scanned
            $buyTicket->delete();
            $successCount++;

            // Track ticket types for response
            $ticketTypeName = $ticket->ticket_type;
            if (!isset($scannedTicketTypes[$ticketTypeName])) {
                $scannedTicketTypes[$ticketTypeName] = [
                    'type' => $ticketTypeName,
                    'qty' => 1
                ];
            } else {
                $scannedTicketTypes[$ticketTypeName]['qty']++;
            }
        }

        // If any tickets were successfully scanned
        if ($successCount > 0) {
            return response()->json([
                'message' => $successCount . ' ticket(s) scanned successfully.',
                'scanned_tickets' => array_values($scannedTicketTypes)
            ], 200);
        }

        // If no tickets were scanned successfully
        return response()->json([
            'error' => $errorMessage ?? 'No tickets could be scanned'
        ], 400);
    }
}
