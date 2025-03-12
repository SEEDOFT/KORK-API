<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEventRequest;
use App\Http\Requests\RegisterOrganizerRequest;
use App\Http\Requests\RegisterTicketRequest;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ['organizer', 'user', 'tickets'];
    private array $event_type = ['concert', 'sport', 'fashion', 'game', 'innovation'];
    private array $ticket_type = ['vvip', 'vip', 'standard', 'normal'];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationship(Event::query(), $this->relations);
        return EventResource::collection($query->get());
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterEventRequest $reqEvent, RegisterOrganizerRequest $reqOrg, RegisterTicketRequest $reqTicket)
    {
        Gate::authorize('create', Event::class);

        $eventData = $reqEvent->validated();
        $orgData = $reqOrg->validated();
        $ticketData = $reqTicket->validated();

        $uploadPath = public_path('event');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($reqEvent->hasFile('poster_url')) {
            $imageName = time() . '.' . $reqEvent->file('poster_url')->extension();
            $reqEvent->file('poster_url')->move($uploadPath, $imageName);
        }

        $result = DB::transaction(function () use ($eventData, $orgData, $ticketData, $imageName) {
            $event = Event::create([
                'event_name' => $eventData['event_name'],
                'event_type' => $eventData['event_type'],
                'description' => $eventData['description'],
                'location' => $eventData['location'],
                'poster_url' => $imageName,
                'start_time' => $eventData['start_time'],
                'end_time' => $eventData['end_time'],
                'user_id' => request()->user()->id,
            ]);
            $event->organizer()->create([
                'name' => $orgData['name'],
                'email' => $orgData['email'],
                'description' => $orgData['description'],
            ]);

            $event->tickets()->createMany(array_map(function ($ticket) {
                return [
                    'ticket_type' => $ticket['ticket_type'],
                    'qty' => $ticket['qty'],
                    'available_qty' => $ticket['qty'],
                    'left_qty' => $ticket['qty'],
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
