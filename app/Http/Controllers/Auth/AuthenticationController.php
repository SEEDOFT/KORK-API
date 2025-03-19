<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    /**
     * Summary of login
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return LoginResource|mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'The provided credentials is incorrect...!'
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'The provided credentials is incorrect...!'
            ], 422);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return LoginResource::make($user, $token);

    }

    /**
     * Summary of logout
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
