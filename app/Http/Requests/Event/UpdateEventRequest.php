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
            'event_name' => ['sometimes', 'string', 'max:255'],
            'event_type' => ['sometimes', 'string', 'max:20'],
            'description' => ['sometimes', 'string'],
            'location' => ['sometimes', 'string', 'max:255'],
            'poster_url' => ['sometimes', 'image', 'mimes:jpeg,png,jpg'],
            'start_date' => ['sometimes', 'required_with:start_time'],
            'start_time' => ['sometimes', 'required_with:start_date'],
            'end_date' => ['sometimes', 'required_with:end_time', 'date', 'after_or_equal:start_date'],
            'end_time' => ['sometimes', 'required_with:end_date'],
        ];
    }
}
