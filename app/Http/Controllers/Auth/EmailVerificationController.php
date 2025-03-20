<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Wotz\VerificationCode\VerificationCode;

class EmailVerificationController extends Controller
{
    public function sendVerifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        if (VerificationCode::send($validated['email'])) {
            return response()->json([
                'error' => 'Cannot send the verification code.'
            ], 400);
        } else {
            return response()->json([
                'message' => 'Verification code has been sent successfully.'
            ], 200);
        }
    }

    public function verifySentCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
            ], 404);
        }

        if (VerificationCode::verify(strval($validated['code']), $validated['email'])) {
            return response()->json([
                'message' => 'Email verified successfully.'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Invalid or expired code.'
            ], 400);
        }
    }
}
