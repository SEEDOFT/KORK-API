<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\BuyTicketRequest;
use App\Http\Resources\Ticket\AllBoughtTicketResource;
use App\Http\Resources\Ticket\SingleBoughtTicketResource;
use App\Models\Attendee;
use App\Models\BuyTicket;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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
            return $allTickets;

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user, BuyTicket $buyTicket)
    {
        return SingleBoughtTicketResource::make($buyTicket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BuyTicket $buyTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuyTicket $buyTicket)
    {
        //
    }
}
