<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterPaymentMethodRequest;
use App\Http\Requests\User\UpdatePaymentMethodRequest;
use App\Http\Resources\User\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PaymentMethodController extends Controller
{

    /**
     * Display all resource
     */
    public function index()
    {
        Gate::authorize('viewAny', PaymentMethod::class);
        $perPage = request()->query('per_page', 15);

        return PaymentMethodResource::collection(PaymentMethod::paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterPaymentMethodRequest $request, User $user)
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
    public function show(User $user, PaymentMethod $paymentMethod)
    {
        Gate::authorize('view', $paymentMethod);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentMethodRequest $reqPayment, User $user, PaymentMethod $paymentMethod)
    {
        Gate::authorize('update', $paymentMethod);

        $validated = $reqPayment->validated();

        $paymentMethod->update($validated);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, PaymentMethod $paymentMethod)
    {
        Gate::authorize('delete', $paymentMethod);

        $paymentMethod->delete();

        return response()->json([
            'message' => 'Payment method has been deleted successfully.'
        ], 204);
    }
}
