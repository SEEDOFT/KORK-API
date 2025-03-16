<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * Login of current user
     * @param \App\Http\Requests\LoginRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'email' => 'The provided credentials is incorrect...!'
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'password' => 'The provided credentials is incorrect...!'
            ], 422);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return LoginResource::make($user, $token);

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
