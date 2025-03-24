<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Ticket\TicketResource;
use App\Http\Resources\User\AllUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'event_name' => $this->event_name,
            'event_type' => $this->event_type,
            'description' => $this->description,
            'location' => $this->location,
            'poster_url' => asset('event/' . $this->poster_url),
            'start_date' => date('Y-m-d', strtotime($this->start_time)),
            'end_date' => date('Y-m-d', strtotime($this->end_time)),
            'start_time' => date('H:i:s', strtotime($this->start_time)),
            'end_time' => date('H:i:s', strtotime($this->end_time)),
            'user' => AllUserResource::make($this->user),
            'attendees' => AttendeeResource::collection($this->attendees),
            'tickets' => TicketResource::collection($this->tickets),
        ];
    }
}
