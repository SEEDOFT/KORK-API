<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CheckPaymentMethodUniqueController extends Controller
{
    public function checkCreditCardNumberUnique(Request $request)
    {
        $cardNum = $request->validate([
            'card_number' => 'required|numeric'
        ]);

        $userId = auth()->id();

        $exists = PaymentMethod::where('card_number', $cardNum)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            return response()->json([
                'message' => 'Card number doesn\'t exist yet.'
            ], 200);
        }

        return response()->json([
            'error' => 'Card number already exists'
        ], 409);
    }
}
