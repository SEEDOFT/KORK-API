<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
            'tickets' => ['sometimes', 'array'],
            'tickets.*.ticket_type' => ['sometimes', 'unique:tickets,ticket_type', 'string', 'in:vvip,vip,standard,normal'],
            'tickets.*.qty' => ['sometimes', 'integer'],
            'tickets.*.price' => ['sometimes', 'numeric']
        ];
    }
}
