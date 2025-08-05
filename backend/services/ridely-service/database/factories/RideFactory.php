<?php

namespace Database\Factories;

use App\Enums\RideStatusEnum;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ride>
 */
class RideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'pick_up' => $this->faker->streetAddress,
            'drop_off' =>  $this->faker->streetAddress,
            'passenger_name' => $this->faker->name,
            'passenger_email' => $this->faker->email,
            'status' => $this->faker->randomElement([
                RideStatusEnum::cases()
            ]),
            'driver_id' => Driver::factory(),
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
