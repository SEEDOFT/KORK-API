<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
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
            'card_number' => $data['card_number'],
            'card_holder_name' => $data['card_holder_name'],
            'expired_date' => $data['expired_date'],
            'cvv' => $data['cvv']
        ]);

        return PaymentMethodResource::make($payment);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        Gate::authorize('view', $paymentMethod);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentMethodRequest $reqPayment, PaymentMethod $paymentMethod)
    {
        Gate::authorize('update', $paymentMethod);

        $paymentData = $reqPayment->validated();

        $paymentMethod->update($paymentData);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        Gate::authorize('delete', $paymentMethod);

        $paymentMethod->delete();

        return response()->json([
            'message' => 'Payment method has been deleted successfully.'
        ], 204);
    }
}
