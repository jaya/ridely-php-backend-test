<?php

namespace App\Converters;

use App\Models\Driver;

class DriverConverter
{
    public static function convertFromArrayToModel(array $data)
    {
        $driver = new Driver();
        $driver->id = $data['id'] ?? null;
        $driver->name = $data['name'] ?? null;
        $driver->car_license_plate = $data['car']['license_plate'] ?? null;
        $driver->car_model = $data['car']['model'] ?? null;
        $driver->car_color = $data['car']['color'] ?? null;
        $driver->available = $data['available'] ?? true;

        return $driver;
    }

    public static function convertFromArrayToResponse(array $driver): array
    {
        $response = [
            'id' => $driver['id'] ?? null,
            'name' => $driver['name'] ?? null,
            'car' => [
                'license_plate' => $driver['car_license_plate'] ?? null,
                'model' => $driver['car_model'] ?? null,
                'color' => $driver['car_color'] ?? null,
            ],
            'available' => $driver['available'] ?? null,
        ];

        if (isset($ride['_links'])) {
            $response['_links'] = $ride['_links'];
        }

        return $response;
    }

    public static function convertListFromArrayToResponse(array $items):array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = self::convertFromArrayToResponse($item);
        }
        return $result;
    }
}