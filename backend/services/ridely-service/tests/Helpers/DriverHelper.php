<?php

namespace Tests\Helpers;

use App\Converters\DriverConverter;
use Faker\Factory as Faker;

class DriverHelper
{
    public static function getDriversListSample(int $count = 5): array
    {
        $drivers = [];

        for ($i = 1; $i <= $count; $i++) {
            $drivers[] = static::getDriverSample($i);
        }

        return $drivers;
    }

    public static function getDriverSample($id = null): array
    {
        $faker = Faker::create();

        return [
            'id' => $id ?? $faker->unique()->numberBetween(1, 1000),
            'name' => $faker->name,
            'car' => [
                'license_plate' => strtoupper($faker->bothify('???####')), // Ex: ABC1234
                'model' => $faker->randomElement(['Tesla Model S', 'Toyota Corolla', 'Ford Focus', 'Honda Civic']),
                'color' => $faker->safeColorName,
            ],
            'available' => $faker->boolean(80), // 80% chance de estar disponível
        ];
    }

    public static function getDriversModelListSample(): array
    {
        $newData = [];
        $data = static::getDriversListSample();
        foreach ($data as $driver) {
            $newData[] = DriverConverter::convertFromArrayToModel($driver);
        }

        return $newData;
    }
}