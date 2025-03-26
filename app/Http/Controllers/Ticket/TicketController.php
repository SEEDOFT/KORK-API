<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\RegisterSingleTicketType;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;

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
    public function store(RegisterSingleTicketType $reqTicket, Event $event)
    {
        if (auth()->id() !== $event->user_id) {
            return response()->json(['error' => 'Unauthorized: You are not the owner of this event.'], 403);
        }

        Gate::authorize('create', [$event]);

        $data = $reqTicket->validated();
        $existingTypes = Ticket::where('event_id', $event->id)->pluck('ticket_type')->toArray();

        if (count($existingTypes) >= 4) {
            return response()->json(['error' => 'This event already has all 4 ticket types. No more can be added.'], 400);
        }

        if (in_array($data['ticket_type'], $existingTypes)) {
            return response()->json(['error' => "The ticket type '{$data['ticket_type']}' already exists for this event."], 400);
        }

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'ticket_type' => $data['ticket_type'],
            'qty' => $data['qty'],
            'available_qty' => $data['qty'],
            'sold_qty' => 0,
            'price' => $data['price'],
        ]);

        return TicketResource::make($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $reqTicket, Event $event, Ticket $ticket)
    {
        if (auth()->id() !== $event->user_id) {
            return response()->json(['error' => 'Unauthorized: You are not the owner of this event.'], 403);
        }

        Gate::authorize('update', $ticket);

        $data = $reqTicket->validated();
        $updateData = [];

        if (isset($data['qty'])) {
            if ($data['qty'] < $ticket->sold_qty) {
                return response()->json(['error' => "New quantity cannot be lower than the number of sold tickets."], 400);
            }

            if ($data['qty'] > $ticket->qty) {
                $updateData['available_qty'] = $ticket->available_qty + ($data['qty'] - $ticket->qty);
            } else {
                $updateData['available_qty'] = max(0, $ticket->available_qty - ($ticket->qty - $data['qty']));
            }

            $updateData['qty'] = $data['qty'];
        }

        if (isset($data['price'])) {
            $updateData['price'] = $data['price'];
        }

        $ticket->update($updateData);

        return TicketResource::make($ticket);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Ticket $ticket)
    {
        if (auth()->id() !== $event->user_id) {
            return response()->json(['error' => 'Unauthorized: You are not the owner of this event.'], 403);
        }

        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully.'], 200);
    }
}
