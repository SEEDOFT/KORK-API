<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterSingleTicketType;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        return TicketResource::collection($event->tickets()->get());
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
            $updateData['qty'] = $ticket->qty + $data['qty'];
            $updateData['available_qty'] = $ticket->available_qty + $data['qty'];
        }

        $ticket->update($updateData);

        return TicketResource::make($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
