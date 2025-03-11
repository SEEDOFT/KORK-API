<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        Gate::authorize('create', Event::class);

        $orgValidated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'description' => 'nullable',
        ]);

        $eventValidated = array_merge(
            $request->validate([
                'event_name' => 'required',
                'event_type' => 'required',
                'description' => 'nullable',
                'location' => 'required',
                'poster_url' => 'required',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
            ]),
            ['user_id' => $request->user()->id],
        );

        DB::beginTransaction();

        try {

            $event = Event::create($eventValidated);
            $event->organizer()->create($orgValidated);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }

        return EventResource::make($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $query = $this->loadRelationship($event->query(), $this->event_type);
        return $this->EventResource::make($query->get());
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
