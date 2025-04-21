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

                DB::transaction(function () use ($ticketsData, $event, &$allTickets, $paymentStatus) {
                    foreach ($ticketsData as $ticketData) {
                        $ticket = Ticket::findOrFail($ticketData['ticket_id']);
                        $requestedQty = $ticketData['qty'];

                        if ($ticket->available_qty < $requestedQty) {
                            throw new Exception("Insufficient quantity");
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
        $results = [];
        $successCount = 0;
        $today = now()->startOfDay();

        foreach ($ticketCodes as $ticketCode) {
            // First find the ticket in BuyTickets based on code
            $buyTicket = BuyTicket::where('ticket_code', $ticketCode)->first();

            if (!$buyTicket) {
                $results[$ticketCode] = 'Ticket not found';
                continue;
            }

            // Get event and ticket details
            $event = Event::find($buyTicket->event_id);
            $ticket = Ticket::find($buyTicket->ticket_id);

            if (!$event || !$ticket) {
                $results[$ticketCode] = 'Invalid event or ticket type';
                continue;
            }

            // Check if user has access to this event
            $userHasAccess = $user->events()->where('id', $event->id)->exists();
            if (!$userHasAccess) {
                $results[$ticketCode] = 'Unauthorized: You do not have access to this event';
                continue;
            }

            // Check if the event starts today
            $eventStartDate = Carbon::parse($event->start_time)->startOfDay();

            if (!$eventStartDate->equalTo($today)) {
                $results[$ticketCode] = 'Cannot scan: Event is not scheduled for today';
                continue;
            }

            // Delete/mark as scanned
            $buyTicket->delete();
            $results[$ticketCode] = 'success';
            $successCount++;
        }

        return response()->json([
            'message' => $successCount . ' ticket(s) scanned successfully.',
            'qty' => $successCount,
            'results' => $results,
            'status' => $successCount > 0 ? 'success' : 'failed'
        ], $successCount > 0 ? 200 : 400);
    }
}
