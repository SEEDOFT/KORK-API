<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_name' => fake()->streetName(),
            'event_type' => fake()->domainName(),
            'description' => fake()->paragraph(),
            'location' => fake()->streetAddress(),
            'poster_url' => fake()->imageUrl(),
            'start_time' => fake()->dateTimeBetween('now', '+1 month'),
            'end_time' => fake()->dateTimeBetween('+1 month', '+2 month')
        ];
    }
}
