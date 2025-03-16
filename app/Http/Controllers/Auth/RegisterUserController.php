<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\RegisterUserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{
    private $columnName = 'email';
    private $value = '';
    /**
     * Summary of register
     * @param \App\Http\Requests\RegisterUserRequest $registerRequest
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $registerRequest)
    {
        $validate = $registerRequest->validated();

        $uploadPath = public_path('user');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($registerRequest->hasFile('profile_url')) {
            $imageName = time() . '.' . $registerRequest->file('profile_url')->extension();
            $registerRequest->file('profile_url')->move($uploadPath, $imageName);
        }

        $user = User::create([
            'first_name' => $validate['first_name'],
            'last_name' => $validate['last_name'],
            'gender' => $validate['gender'],
            'dob' => $validate['dob'],
            'nationality' => $validate['nationality'] ?? null,
            'profile_url' => $imageName,
            'location' => $validate['location'],
            'phone_number' => $validate['phone_number'] ?? null,
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('api-token')->plainTextToken;
        return RegisterUserResource::make($user, $token);
    }
    public function checkColumnUnique(Request $request)
    {
        $email = $request->validate(['email' => ['email']]);
        $unique = User::where('email', $email)->exists();

        if ($unique) {
            return response()->json([
                'email' => 'Email already exists'
            ], 409);
        }

        return response()->json([
            'email' => 'Email doesn\'t exist yet.'
        ], 200);
    }
}
