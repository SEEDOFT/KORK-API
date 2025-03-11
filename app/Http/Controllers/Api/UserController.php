<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterUserRequest $registerRequest)
    {
        $validate = $registerRequest->validated();
        $user = User::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'gender' => $validate['gender'],
            'dob' => $validate['dob'],
            'nationality' => $validate['nationality'] ?? null,
            'profile_url' => $validate['profile_url'],
            'location' => $validate['location'],
            'phone_number' => $validate['phone_number'] ?? null,
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
        ]);

        return UserResource::make([
            $user,
            'register_token' => $user->createToken('api-token')->plainTextToken,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $updateRequest, User $user)
    {
        $user->update($updateRequest->validated());

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User has been deleted successfully.'
        ], 204);
    }
}
