<?php

namespace App\Converters;

use App\Models\Driver;

class DriverConverter
{
    public static function convertFromArrayToModel(array $data)
    {
        $driver = new Driver();

        $driver->name = $data['name'] ?? null;
        $driver->car_license_plate = $data['car']['license_plate'] ?? null;
        $driver->car_model = $data['car']['model'] ?? null;
        $driver->car_color = $data['car']['color'] ?? null;
        $driver->available = $data['available'] ?? true;

        return $driver;
    }

    public function convertFromModelToArray(Driver $driver): array
    {
        // TODO ver a possibilidade do uso $driver->toArray()
        return [
            'id' => $driver->id,
            'name' => $driver->name,
            'car' => [
                'license_plate' => $driver->car_license_plate,
                'model' => $driver->car_model,
                'color' => $driver->car_color
            ],
            'available' => $driver->available,
        ];
    }
}