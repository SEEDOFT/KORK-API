<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Wotz\VerificationCode\VerificationCode;

class PasswordResetController extends Controller
{
    /**
     * Summary of __invoke
     * @param PasswordResetRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function __invoke(PasswordResetRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        if (isset($validated['current_password'])) {
            if (Hash::check($validated['current_password'], $user->password)) {
                $user->update([
                    'email_verified_at' => now(),
                    'password' => Hash::make($validated['password'])
                ]);
                return response()->json([
                    'message' => 'Password changed successfully'
                ], 200);
            }
            return response()->json(['error' => 'Incorrect current password'], 400);
        } elseif (isset($validated['code'])) {
            if (VerificationCode::verify(strval($validated['code']), $user->email)) {
                $user->update([
                    'password' => Hash::make($validated['password'])
                ]);
                return response()->json(['message' => 'Password changed successfully'], 200);
            }
            return response()->json(['error' => 'Invalid or expired verification code'], 400);
        }
        return response()->json(['error' => 'Please provide current password or verification code'], 400);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            if (!isset($validated['code'])) {
                return response()->json(['error' => 'Code is required to reset password'], 400);

            } elseif (VerificationCode::verify(strval($validated['code']), $validated['email'])) {
                $user->update([
                    'email_verified_at' => now(),
                    'password' => Hash::make($validated['password'])
                ]);
                return response()->json(['message' => 'Password reset successfully'], 200);

            }

            return response()->json(['error' => 'Invalid or expired verification code'], 400);
        }
        return response()->json(['error' => 'Email is required to reset password'], 400);
    }
}
