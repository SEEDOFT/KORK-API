<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $orgs = Organizer::all();

        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $org = $orgs->random();

            Event::factory()->create([
                'user_id' => $user->id,
                'organizer_id' => $org->id,
            ]);


        }

    }
}
