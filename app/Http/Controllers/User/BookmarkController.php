<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\BookmarkResource;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $bookmark = $user->bookmarks()->get();

        return BookmarkResource::collection($bookmark);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $eventId = $request->validate([
            'event_id' => ['required', 'unique:bookmarks,event_id', 'exists:events,id']
        ]);

        $bookmark = Bookmark::create([
            'event_id' => $eventId['event_id'],
            'user_id' => request()->user()->id,
        ]);

        return BookmarkResource::make($bookmark);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Bookmark $bookmark)
    {
        return BookmarkResource::make($bookmark);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Bookmark $bookmark)
    {
        $bookmark->delete();

        return response()->json([
            'message' => 'Bookmark deleted successfully.'
        ], 204);
    }
}
