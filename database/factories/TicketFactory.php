<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
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
