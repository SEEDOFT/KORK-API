<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ['profile', 'payment_method'];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = $this->loadRelationship(User::query(), $this->relations);
        return UserResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'password' => 'required',
            'gender' => 'required|string|max:10',
            'dob' => 'required|date',
            'nationality' => 'required|string',
            'profile_url' => 'nullable',
            'location' => 'required|string|max:255'
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $validated['last_name'] . ' ' . $validated['first_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password' => bcrypt($validated['password']),
            ]);
            $user->profile()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'dob' => $validated['dob'],
                'nationality' => $validated['nationality'],
                'profile_url' => $validated['profile_url'],
                'location' => $validated['location'],
            ]);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }

        return UserResource::make($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $query = $this->loadRelationship($user->query(), $this->relations);
        return UserResource::make($query->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validate request data
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone_number' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8',
            'gender' => 'sometimes|string|in:male,female,other',
            'dob' => 'sometimes|date|before:today',
            'nationality' => 'sometimes|string|max:50',
            'profile_url' => 'sometimes|url',
            'location' => 'sometimes|string|max:255'
        ]);

        DB::transaction(function () use ($validated, $user) {
            // Update user model
            $userData = [];

            // Handle name construction
            if (isset($validated['first_name']) || isset($validated['last_name'])) {
                $firstName = $validated['first_name'] ?? $user->profile->first_name;
                $lastName = $validated['last_name'] ?? $user->profile->last_name;
                $userData['name'] = "$lastName $firstName";
            }

            // Handle other user fields
            foreach (['email', 'phone_number'] as $field) {
                if (isset($validated[$field])) {
                    $userData[$field] = $validated[$field];
                }
            }

            // Handle password separately
            if (isset($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }

            // Update user if there's data
            if (!empty($userData)) {
                $user->updateOrFail($userData);
            }

            // Update profile
            $profileData = collect($validated)->only([
                'first_name',
                'last_name',
                'gender',
                'dob',
                'nationality',
                'profile_url',
                'location'
            ])->filter()->toArray();

            if (!empty($profileData)) {
                $user->profile()->updateOrFail($profileData);
            }
        });

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return response()->json(status: 204);
    }
}
