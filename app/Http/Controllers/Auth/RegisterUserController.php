<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\User\RegisterUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Wotz\VerificationCode\VerificationCode;

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

        if (!VerificationCode::verify(strval($validate['code']), $validate['email'])) {
            return response()->json(['error' => 'Invalid verification.'], 422);
        }

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


        $token = $user->createToken('api-token')->plainTextToken;
        return RegisterUserResource::make($user, $token);
    }
}
