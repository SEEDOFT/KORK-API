<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'dob' => $this->dob,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'nationality' => $this->nationality,
            'gender' => $this->gender,
            'profile_url' => asset('user/' . $this->profile_url),
            'location' => $this->location,
            'payment_methods' => PaymentMethodResource::collection($this->payment_methods),
        ];
    }
}
