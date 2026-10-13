<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckEmailUniqueController extends Controller
{
    /**
     * Check the Email Unique
     *
     * @return mixed|JsonResponse
     */
    public function checkColumnUnique(Request $request)
    {
        $email = $request->validate(['email' => ['email']]);
        $unique = User::where('email', $email)->exists();

        if ($unique) {
            return response()->json([
                'email' => 'Email already exists',
            ], 409);
        }

        return response()->json([
            'email' => 'Email doesn\'t exist yet.',
        ], 200);
    }
}
