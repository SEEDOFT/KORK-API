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
        $attendeesData = $this->attendees->groupBy('user_id')->map(function ($attendees) {
            $user = $attendees->first()->user;
            return [
                'user_id' => $attendees->first()->user_id,
                'profile_url' => asset('user/' . $user->profile_url),
                'qty' => $attendees->count(),
            ];
        });
        $attendeesArray = $attendeesData->values()->toArray();
        $eventTypes = [
            "តន្ត្រី" => ["តន្ត្រី", "concert"],
            "concert" => ["តន្ត្រី", "concert"],
            "ហ្គេម" => ["ហ្គេម", "game"],
            "game" => ["ហ្គេម", "game"],
            "ម៉ូដ" => ["ម៉ូដ", "fashion"],
            "fashion" => ["ម៉ូដ", "fashion"],
            "កីឡា" => ["កីឡា", "sport"],
            "sport" => ["កីឡា", "sport"],
            "ការច្នៃប្រឌិត" => ["ការច្នៃប្រឌិត", "innovation"],
            "innovation" => ["ការច្នៃប្រឌិត", "innovation"],
        ];

        return [
            'id' => $this->id,
            'event_name' => $this->event_name,
            'event_type' => $eventTypes[$this->event_type] ?? [$this->event_type],
            'description' => $this->description,
            'location' => $this->location,
            'poster_url' => asset('event/' . $this->poster_url),
            'start_date' => date('Y-m-d', strtotime($this->start_time)),
            'end_date' => date('Y-m-d', strtotime($this->end_time)),
            'start_time' => date('H:i:s', strtotime($this->start_time)),
            'end_time' => date('H:i:s', strtotime($this->end_time)),
            'user' => AllUserResource::make($this->user),
            'attendees' => $attendeesArray,
            'tickets' => TicketResource::collection($this->tickets),
        ];
    }

}
