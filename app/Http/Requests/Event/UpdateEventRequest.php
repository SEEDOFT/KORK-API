<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'event_name' => ['sometimes', 'string', 'max:150'],
            'event_type' => ['sometimes', 'string', 'in:fashion, innovation, concert, sport, game', 'max:20'],
            'description' => ['sometimes'],
            'location' => ['sometimes', 'string'],
            'poster_url' => ['sometimes', 'image'],
            'start_time' => ['sometimes', 'date'],
            'end_time' => ['sometimes', 'date', 'after:start_time'],
        ];
    }
}
