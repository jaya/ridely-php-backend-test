<?php

namespace Tests\Helpers;

use App\Converters\DriverConverter;

class DriverHelper
{
    public static function getDriversListSample(): array
    {
        return [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith']
        ];
    }

    public static function getDriverSample(): array
    {
        return [
            'id' => 1,
            'name' => 'John Doe',
            'car' => [
                'license_plate' => 'XYZ1234',
                'model' => 'Tesla Model S',
                'color' => 'Black',
            ],
            'available' => true,
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