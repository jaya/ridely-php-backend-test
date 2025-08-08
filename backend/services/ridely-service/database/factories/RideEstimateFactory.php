<?php

namespace Database\Factories;

use App\Enums\RideEstimateStatusEnum;
use App\Models\Ride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ride>
 */
class RideEstimateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = array_map(fn($case) => $case->value, RideEstimateStatusEnum::cases());
        return [
            'status' => ($this->faker->randomElement($statuses)),
            'ride_id' => Ride::factory(),
            'distance_km' => $this->faker->randomFloat(2, 1, 100),
            'duration_min' => $this->faker->randomFloat(2, 1, 100),
            'price_estimate' => $this->faker->randomFloat(2, 1, 100),
        ];

    }
}
