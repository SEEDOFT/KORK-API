<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Event\EventResource;
use App\Http\Resources\User\AllUserResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllBoughtTicketResource extends JsonResource
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
            'event' => EventResource::make($this->event),
            'ticket' => TicketResource::make($this->ticket),
            'user' => AllUserResource::make($this->user),
            'ticket_code' => $this->ticket_code,
            'price' => $this->price,
            'payment_status' => $this->payment_status,
            'buy_at' => date("Y-m-d H:i:s", strtotime($this->created_at))
        ];
    }
}
