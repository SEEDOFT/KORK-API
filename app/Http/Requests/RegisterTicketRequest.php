<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterTicketRequest extends FormRequest
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
            'tickets' => ['required', 'array'],
            'tickets.*.ticket_type' => ['required', 'unique:tickets,ticket_type', 'string', 'in:vvip,vip,standard,normal'],
            'tickets.*.qty' => ['required', 'integer'],
            'tickets.*.price' => ['required', 'numeric']
        ];
    }
}
