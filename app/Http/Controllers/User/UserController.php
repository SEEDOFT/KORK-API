<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\AllUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    /**
     * Display all user
     */
    public function index()
    {
        return AllUserResource::collection(User::get());
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $updateRequest, User $user)
    {

        Gate::authorize('update', $user);

        $data = $updateRequest->validated();

        $uploadPath = public_path('user');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($updateRequest->hasFile('profile_url')) {
            if (!empty($user->profile_url) && file_exists($uploadPath . '/' . $user->profile_url)) {
                unlink($uploadPath . '/' . $user->profile_url);
            }

            $imageName = time() . '.' . $updateRequest->file('profile_url')->extension();
            $updateRequest->file('profile_url')->move($uploadPath, $imageName);

            $data['profile_url'] = $imageName;
        }

        $user->update($data);

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $imagePath = public_path('user/' . $user->profile_url);
        if (file_exists($imagePath) && is_file($imagePath)) {
            unlink($imagePath);
        }

        $user->delete();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'User has been deleted successfully.'
        ], 204);
    }
}
