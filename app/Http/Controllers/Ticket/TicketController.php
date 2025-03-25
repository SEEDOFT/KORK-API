<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\RegisterSingleTicketType;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Event;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $perPage = request()->query('per_page', 15);
        return TicketResource::collection($event->tickets()->paginate($perPage));
    }

    /**
     * Store single ticket
     */
    public function store(RegisterSingleTicketType $reqTicket, Event $event, Ticket $ticket)
    {
        $data = $reqTicket->validated();
        $tk = $ticket->create([
            'event_id' => $event->id,
            'ticket_type' => $data['ticket_type'],
            'qty' => $ticket->qty + $data['qty'],
            'available_qty' => $data['qty'],
            'sold_qty' => 0,
            'price' => $data['price'],
        ]);

        return TicketResource::make($tk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $reqTicket, Event $event, Ticket $ticket)
    {
        $data = $reqTicket->validated();
        $updateData = $data;

        if (isset($data['qty'])) {
            if ($data['qty'] > $ticket->sold_qty && $data['qty'] > $ticket->qty) {
                $updateData['available_qty'] = $ticket->available_qty + ($data['qty'] - $ticket->qty);
            } elseif ($data['qty'] < $ticket->qty && $data['qty'] >= $ticket->sold_qty) {
                $updateData['available_qty'] = max(0, $ticket->available_qty - ($ticket->qty - $data['qty']));
            } elseif ($data['qty'] < $ticket->sold_qty) {
                return response()->json(['error' => "New quantity cannot be lower than sold tickets"], 400);
            }

            $updateData['qty'] = $data['qty'];
        }

        $ticket->update($updateData);

        return TicketResource::make($ticket);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Ticket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket has been deleted successfully.'
        ], 204);
    }
}
