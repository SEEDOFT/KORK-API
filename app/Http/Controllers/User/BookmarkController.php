<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventResource;
use App\Http\Resources\User\BookmarkResource;
use App\Models\Bookmark;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        if ($user->id !== auth()->user()->id) {
            return response()->json(['error' => 'You can only view your own bookmarks.'], 403);
        }

        Gate::authorize('viewAny', $user);

        $perPage = request()->query('per_page', 15);

        $events = Event::whereIn('id', $user->bookmarks()->pluck('event_id'));

        return EventResource::collection($events->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        if ($request->user()->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        Gate::authorize('create', [Bookmark::class, $user]);

        $validated = $request->validate([
            'event_id' => [
                'required',
                Rule::unique('bookmarks')->where(fn($query) => $query->where('user_id', $user->id)),
                'exists:events,id'
            ],
        ]);

        $bookmark = Bookmark::create([
            'event_id' => $validated['event_id'],
            'user_id' => $user->id,
        ]);

        return BookmarkResource::make($bookmark);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user, Bookmark $bookmark)
    {
        if ($user->id !== auth()->user()->id) {
            return response()->json(['error' => 'You can only view your own bookmarks.'], 403);
        }
        Gate::authorize('view', $bookmark);

        return BookmarkResource::make($bookmark);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, $event_id)
    {
        Gate::authorize('delete', $user);

        $bookmark = Bookmark::where('event_id', $event_id)->first();

        if (!$bookmark) {
            return response()->json([
                'message' => 'Bookmark not found.'
            ], 404);
        }

        $bookmark->delete();

        return response()->json([
            'message' => 'Bookmark deleted successfully.'
        ], 204);
    }

}
