<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Summary of __invoke
     * @param PasswordResetRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function __invoke(PasswordResetRequest $request)
    {
        $request->validated();
        auth()->user()->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json([
            'message' => 'Password has been reset successfully.'
        ], 205);
    }
}
