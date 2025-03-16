<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSingleTicketType extends FormRequest
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
            'ticket_type' => ['required', 'string', 'in:vvip,vip,standard,normal', 'unique:tickets,ticket_type'],
            'qty' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0']
        ];
    }
}
