<?php

namespace App\Converters;

use App\Models\Driver;

class DriverConverter
{
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