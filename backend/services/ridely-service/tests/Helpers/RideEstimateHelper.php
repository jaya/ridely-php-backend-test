<?php

namespace Tests\Helpers;

use App\Enums\RideEstimateStatusEnum;
use Faker\Factory as Faker;

class RideEstimateHelper
{
    public static function getRideEstimateListSample(int $count = 5): array
    {
        $faker = Faker::create();
        $estimates = [];

        for ($i = 1; $i <= $count; $i++) {
            $estimates[] = static::getRideEstimateSample($i);
        }

        return $estimates;
    }

    public static function getRideEstimateSample($id = null): array
    {
        $faker = Faker::create();

        return [
            'id' => $id ?? $faker->unique()->numberBetween(1, 1000),
            'distance_km' => $faker->randomFloat(2, 1, 50),     // e.g. 1.23 to 50.00 km
            'duration_min' => $faker->numberBetween(5, 180),     // duration in minutes
            'price_estimate' => $faker->randomFloat(2, 10, 500), // price in your currency
            'status' => $faker->randomElement(array_map(fn($case) => $case->value, RideEstimateStatusEnum::cases())),
            'created_at' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
            'updated_at' => $faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
