<?php

namespace App\Http\Requests\Ticket;

use App\Models\Ticket;
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
            'ticket_type' => [
                'required',
                'string',
                'in:vvip,vip,standard,normal',
                function ($attribute, $value, $fail) {
                    $eventId = request()->input('event_id');
                    $existingTypes = Ticket::where('event_id', $eventId)->pluck('ticket_type')->toArray();

                    if (count($existingTypes) >= 4) {
                        $fail("This event already has all 4 ticket types. No more can be added.");
                    }

                    if (in_array($value, $existingTypes)) {
                        $fail("The ticket type '{$value}' is already assigned to this event.");
                    }

                    $allowedTypes = ['vvip', 'vip', 'standard', 'normal'];
                    $remainingTypes = array_diff($allowedTypes, $existingTypes);

                    if (count($existingTypes) == 2 && !in_array($value, $remainingTypes)) {
                        $fail("You can only add the remaining ticket types: " . implode(', ', $remainingTypes));
                    }
                }
            ],
            'qty' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0']
        ];
    }
}
