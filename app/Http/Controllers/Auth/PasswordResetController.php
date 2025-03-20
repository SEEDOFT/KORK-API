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
        // $data = $request->validated();
        // if (isset($data['current_password'])) {
        //     auth()->user()->update([
        //         'password' => Hash::make($data['password'])
        //     ]);
        // } elseif (isset($data['code'])) {
        //     VerificationCode::verify(strval($data['code']), request()->user()->email)
        //         ? auth()->user()->update(['password' => Hash::make($data['password'])])
        //         : 'error';
        // }

        // return response()->json([
        //     'message' => 'Password has been reset successfully.'
        // ], 205);

        return response()->json([
            'token'
        ]);
    }

    public function resetPassword(PasswordResetRequest $request, User $user)
    {
        $validated = $request->validated();

        if (isset($data['current_password'])) {
            if ($user->email == $validated['email']) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
        }

        // } elseif (isset($data['code'])) {
        //     VerificationCode::verify(strval($data['code']), $data['email']);
        // }

        return response()->json([
            'no-token'
        ]);
    }
}
