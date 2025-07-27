<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'        => $this->faker->unique()->word(),
            'property_id' => Property::factory(),
            'price'       => $this->faker->numberBetween(50, 500),
            'max_guests'  => $this->faker->numberBetween(1, 5),
        ];
    }
}
