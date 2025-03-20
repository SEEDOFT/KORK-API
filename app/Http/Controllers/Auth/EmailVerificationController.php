<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wotz\VerificationCode\VerificationCode;

class EmailVerificationController extends Controller
{
    public function sendVerifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        if (!VerificationCode::send($validated['email'])) {
            return response()->json([
                'message' => 'Verification code has been sent successfully.'
            ]);
        } else {
            return response()->json([
                'error' => 'Cannot send the verification code.'
            ]);
        }
    }

    public function verifySentCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        if (VerificationCode::verify(strval($validated['code']), $validated['email'])) {
            return response()->json([
                'message' => 'Verification successfully.'
            ]);
        } else {
            return response()->json([
                'error' => 'Cannot verify the email.'
            ]);
        }
    }
}
