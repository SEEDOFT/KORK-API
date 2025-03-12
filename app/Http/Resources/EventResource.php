<?php

namespace App\Http\Resources;

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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'organizer' => OrganizerResource::make($this->whenLoaded('organizer')),
            'user' => AllUserResource::make($this->whenLoaded('user')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
