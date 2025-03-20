<?php

namespace App\Http\Resources\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'ticket_type' => $this->ticket_type,
            'qty' => $this->qty,
            'available_qty' => $this->available_qty,
            'sold_qty' => $this->sold_qty,
            'price' => $this->price,
        ];
    }
}
