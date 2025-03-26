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
     * Display all payment methods for the authenticated user.
     */
    public function index(User $user)
    {
        $authUser = auth()->user();
        if ($authUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized: You can only view your own payment methods.'], 403);
        }

        Gate::authorize('viewAny', PaymentMethod::class);

        $perPage = request()->query('per_page', 15);

        return PaymentMethodResource::collection(
            PaymentMethod::where('user_id', $user->id)->paginate($perPage)
        );
    }


    /**
     * Store a newly created payment method.
     */
    public function store(RegisterPaymentMethodRequest $request, User $user)
    {
        $authUser = auth()->user();

        if ($authUser->id !== $user->id) {
            return response()->json(['error' => 'Unauthorized: You can only add a payment method for your own account.'], 403);
        }

        Gate::authorize('create', [PaymentMethod::class, $user]);

        $data = $request->validated();

        $payment = PaymentMethod::create([
            'user_id' => $user->id,
            'card_number' => $data['card_number'],
            'card_holder_name' => $data['card_holder_name'],
            'expired_date' => $data['expired_date'],
            'cvv' => $data['cvv']
        ]);

        return PaymentMethodResource::make($payment);
    }

    /**
     * Display a specific payment method.
     */
    /**
     * Display a specific payment method.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        $authUser = auth()->user();

        if ($authUser->id !== $paymentMethod->user_id) {
            return response()->json(['error' => 'Unauthorized: You can only view your own payment methods.'], 403);
        }

        Gate::authorize('view', $paymentMethod);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Update an existing payment method.
     */
    public function update(UpdatePaymentMethodRequest $reqPayment, User $user, PaymentMethod $paymentMethod)
    {
        $authUser = auth()->user();

        if ($authUser->id !== $paymentMethod->user_id) {
            return response()->json(['error' => 'Unauthorized: You can only update your own payment methods.'], 403);
        }

        Gate::authorize('update', $paymentMethod);

        $validated = $reqPayment->validated();
        $paymentMethod->update($validated);

        return PaymentMethodResource::make($paymentMethod);
    }

    /**
     * Remove a payment method.
     */
    public function destroy(User $user, PaymentMethod $paymentMethod)
    {
        $authUser = auth()->user();

        if ($authUser->id !== $paymentMethod->user_id) {
            return response()->json(['error' => 'Unauthorized: You can only delete your own payment methods.'], 403);
        }

        Gate::authorize('delete', $paymentMethod);

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method has been deleted successfully.'], 204);
    }

}
