<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        for ($i = 0; $i < count($events); $i++) {
            Ticket::factory()->create([
                'event_id' => $events->random()->id,
            ]);
        }
    }
}
