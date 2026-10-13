<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_type' => fake()->randomElement(['VVIP', 'VIP', 'Standard', 'Normal']),
            'qty' => $qty = fake()->numberBetween(10, 100),
            'available_qty' => $qty,
            'left_qty' => $qty,
            'price' => fake()->numberBetween(1, 10),
        ];
    }
}
