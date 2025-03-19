<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\BuyTicketRequest;
use App\Http\Resources\Ticket\AllBoughtTicketResource;
use App\Http\Resources\Ticket\SingleBoughtTicketResource;
use App\Models\BuyTicket;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BuyTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user, Event $event)
    {

        $boughtTicket = $user->buyTickets()->get();
        return AllBoughtTicketResource::collection($boughtTicket);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuyTicketRequest $request, Event $event)
    {
        $ticketsData = $request->validated()['tickets'];
        $allBoughtTicket = [];

        DB::transaction(function () use ($ticketsData, $event, &$allBoughtTicket) {
            foreach ($ticketsData as $ticketData) {
                $ticket = Ticket::findOrFail($ticketData['ticket_id']);
                $requestedQty = $ticketData['qty'];

                if ($ticket->available_qty < $requestedQty) {
                    return response()->json([
                        'error' => "Insufficient quantity for ticket id: {$ticket->id}"
                    ], 400);
                }

                $ticket->available_qty -= $requestedQty;
                $ticket->sold_qty += $requestedQty;
                $ticket->save();

                for ($i = 0; $i < $requestedQty; $i++) {
                    do {
                        $ticketCode = strtoupper(Str::random(15));
                    } while (BuyTicket::where('ticket_code', $ticketCode)->exists());

                    $allBoughtTicket[] = BuyTicket::create([
                        'event_id' => $event->id,
                        'ticket_id' => $ticket->id,
                        'user_id' => request()->user()->id,
                        'ticket_code' => $ticketCode,
                        'price' => $ticket->price,
                        'payment_status' => false,
                    ]);
                }
            }
        });

        return AllBoughtTicketResource::collection($allBoughtTicket);
        // return $result;
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
