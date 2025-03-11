<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials is incorrect...!']
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided credentials is incorrect...!']
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'login_token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

    public function __invoke(PasswordResetRequest $request)
    {
        $request->validated();
        auth()->user()->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json([
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
