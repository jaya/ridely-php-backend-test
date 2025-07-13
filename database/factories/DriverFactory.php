<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'available' => true,
            'activation_date' => now()->subDays(rand(1, 10)),
            'latitude' => $this->faker->latitude(-10.95, -10.85),
            'longitude' => $this->faker->longitude(-37.10, -37.00),
            'car_license_plate' => strtoupper($this->faker->bothify('???####')),
            'car_model' => $this->faker->randomElement(['Corolla', 'Civic', 'Onix']),
            'car_color' => $this->faker->safeColorName()
        ];
    }
}
