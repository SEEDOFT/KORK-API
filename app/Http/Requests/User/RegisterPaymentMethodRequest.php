<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'card_number' => ['required', 'numeric'],
            'card_holder_name' => ['required', 'string'],
            'expired_date' => ['required', 'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/'],
            'cvv' => ['required', 'numeric']
        ];
    }
}
