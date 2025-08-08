<?php

namespace Tests\Helpers;

use App\Converters\DriverConverter;
use App\Enums\RideStatusEnum;
use App\Models\Ride;

class RideHelper
{
    public static function getRidesListSample(): array
    {
        return [
//            ['id' => 1, 'name' => 'John Doe'],
//            ['id' => 2, 'name' => 'Jane Smith']
        ];
    }

    public static function getRidesSample(): array
    {
        return [
//            'id' => 1,
//            'name' => 'John Doe',
//            'car' => [
//                'license_plate' => 'XYZ1234',
//                'model' => 'Tesla Model S',
//                'color' => 'Black',
//            ],
//            'available' => true,
        ];
    }

    public static function getRidesModelListSample(): array
    {
        $newData = [];
//        $data = static::getRidesListSample();
//        foreach ($data as $driver) {
//            $newData[] = DriverConverter::convertFromArrayToModel($driver);
//        }

        return $newData;
    }

    /**
     * @param string|null $pickUp
     * @param string|null $dropOff
     * @return Ride|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function createRide(string $pickUp = null, string $dropOff = null)
    {
        if (!$pickUp) {
            $pickUp = "Avenida Beira Mar, 25";
        }
        if (!$dropOff) {
            $dropOff = "Avenida Euclides Figueiredo, 65";
        }


        return Ride::factory()->create([
            'status' => RideStatusEnum::REQUESTED->value,
            'pick_up' => $pickUp,
            'drop_off' => $dropOff,
        ]);
    }
}