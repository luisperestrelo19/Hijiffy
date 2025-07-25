<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::factory()
            ->count(10)
            ->create()
            ->each(function (Property $property) {
                $property->rooms()->saveMany(
                    Room::factory()->count(3)->create()->each(function (Room $room) {
                        $availabilities = collect(range(0, 4))->map(function ($i) {
                            return Availability::factory()->make([
                                'date' => now()->addDays($i),
                            ]);
                        });

                        $room->availabilities()->saveMany($availabilities);
                    })
                );
            });
    }
}
