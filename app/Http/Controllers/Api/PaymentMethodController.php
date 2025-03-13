<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class PaymentMethodController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterPaymentMethodRequest $request)
    {
        Gate::authorize('create', PaymentMethod::class);

        $data = $request->validated();

        $payment = PaymentMethod::create([
            'user_id' => request()->user()->id,
            'card_number' => Hash::make($data['card_number']),
            'card_holder_name' => $data['card_holder_name'],
            'expired_date' => $data['expired_date'],
        ]);

        return PaymentMethodResource::make($payment);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        //
    }
}
