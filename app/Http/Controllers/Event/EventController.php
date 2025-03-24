<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\RegisterEventRequest;
use App\Http\Requests\Event\RegisterOrganizerRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Requests\Event\UpdateOrganizerRequest;
use App\Http\Requests\Ticket\RegisterTicketRequest;
use App\Http\Resources\Event\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Http\Traits\FilterColumn;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use FilterColumn;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Event::query();
        // $perPage = request()->query('per_page', 15);

        // $query = $this->applyFilter($query, 'event_type');
        // $query = $this->applySearch($query, 'event_name');
        // $query = $this->applyPriceRange($query);
        // $query = $this->applyDateRange($query, 'start_time');

        // $events = $query->paginate($perPage);

        // return EventResource::collection($events);
        return EventResource::collection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterEventRequest $reqEvent, RegisterTicketRequest $reqTicket)
    {
        Gate::authorize('create', Event::class);

        $eventData = $reqEvent->validated();
        $ticketData = $reqTicket->validated();

        $uploadPath = public_path('event');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($reqEvent->hasFile('poster_url')) {
            $imageName = time() . '.' . $reqEvent->file('poster_url')->extension();
            $reqEvent->file('poster_url')->move($uploadPath, $imageName);
        }

        $result = DB::transaction(function () use ($eventData, $ticketData, $imageName) {
            $event = Event::create([
                'event_name' => $eventData['event_name'],
                'event_type' => $eventData['event_type'],
                'description' => $eventData['description'],
                'location' => $eventData['location'],
                'poster_url' => $imageName,
                'start_time' => $eventData['start_date'] . ' ' . $eventData['start_time'],
                'end_time' => $eventData['end_date'] . ' ' . $eventData['end_time'],
                'user_id' => request()->user()->id,
            ]);

            $event->tickets()->createMany(array_map(function ($ticket) {
                return [
                    'ticket_type' => $ticket['ticket_type'],
                    'qty' => $ticket['qty'],
                    'available_qty' => $ticket['qty'],
                    'sold_qty' => 0,
                    'price' => $ticket['price'],
                ];
            }, $ticketData['tickets']));
            return $event;
        });
        return EventResource::make($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return EventResource::make($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $reqEvent, Event $event)
    {
        Gate::authorize('update', $event);

        $eventData = $reqEvent->validated();

        $uploadPath = public_path('event');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($reqEvent->hasFile('poster_url')) {
            if (!empty($uploadPath . '/' . $event->poster_url)) {
                unlink($uploadPath . '/' . $event->poster_url);
            }

            $imageName = time() . '.' . $reqEvent->file('poster_url')->extension();
            $reqEvent->file('poster_url')->move($uploadPath, $imageName);

            $eventData['poster_url'] = $imageName;
        }

        $event->update($eventData);

        return EventResource::make($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $imagePath = public_path('event/' . $event->poster_url);

        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event has been deleted successfully.'
        ], 204);
    }
}
