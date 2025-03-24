<?php

namespace App\Http\Requests\Ticket;

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
            'tickets' => ['required', 'array', 'max:4'],
            'tickets.*.ticket_type' => ['required', 'string', 'in:vvip,vip,standard,normal', 'distinct'],
            'tickets.*.qty' => ['required', 'integer', 'min:10'],
            'tickets.*.price' => ['required', 'numeric', 'min:0']
        ];
    }
}
