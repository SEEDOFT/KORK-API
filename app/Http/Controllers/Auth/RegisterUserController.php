<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{
    /**
     * Summary of register
     * @param RegisterUserRequest $registerRequest
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $registerRequest)
    {
        $validate = $registerRequest->validated();
        $gender = '';

        $uploadPath = public_path('user');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($registerRequest->hasFile('profile_url')) {
            $imageName = time() . '.' . $registerRequest->file('profile_url')->extension();
            $registerRequest->file('profile_url')->move($uploadPath, $imageName);
        }

        if ($validate['gender'] == 'ប្រុស') {
            $gender = 'male';
        } elseif ($validate['gender'] == 'ស្រី') {
            $gender = 'female';
        } elseif ($validate['gender'] == 'ផ្សេងទៀត') {
            $gender = 'other';
        } else {
            $gender = $validate['gender'];
        }

        $user = User::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'gender' => $gender,
            'dob' => $validate['dob'],
            'nationality' => $validate['nationality'] ?? null,
            'profile_url' => $imageName,
            'location' => $validate['location'],
            'phone_number' => $validate['phone_number'] ?? null,
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;
        return UserResource::make($user);
    }
}
