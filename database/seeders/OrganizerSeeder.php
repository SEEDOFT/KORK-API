<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        for ($i = 0; $i < count($events); $i++) {
            $event = $events->random();
            Organizer::factory()->create([
                'event_id' => $event->id
            ]);
        }
    }
}
