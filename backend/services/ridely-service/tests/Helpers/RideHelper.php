<?php

namespace Tests\Helpers;

use App\Converters\DriverConverter;
use App\Converters\RideConverter;
use App\Enums\RideStatusEnum;
use App\Models\Ride;
use Faker\Factory as Faker;

class RideHelper
{
    public static function getRideListSample(int $count = 5): array
    {
        $faker = Faker::create();
        $drivers = [];

        for ($i = 1; $i <= $count; $i++) {
            $drivers[] = static::getRideSample($i);
        }

        return $drivers;
    }

    public static function getRideSample($id = null): array
    {
        $faker = Faker::create();

        return [
            'id' => $id ?? $faker->unique()->numberBetween(1, 1000),
            'status' => $faker->randomElement(array_map(fn($case) => $case->value, RideStatusEnum::cases())),
            'pick_up' => $faker->streetAddress(),
            'drop_off' => $faker->streetAddress(),
            'passenger' => [
                'name' => $faker->name,
                'email' => $faker->email,
            ]
        ];
    }

    public static function getRideModelListSample(): array
    {
        $newData = [];
        $data = static::getRideListSample();
        foreach ($data as $ride) {
            $newData[] = RideConverter::convertFromArrayToModel($ride);
        }

        return $newData;
    }


}