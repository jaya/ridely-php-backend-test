<?php

namespace App\Converters;

use App\Models\Ride;

class RideConverter
{
    public static function convertFromArrayToModel(array $data)
    {
        $ride = new Ride();
        $ride->id = $data['id'] ?? null;
        $ride->status = $data['status'] ?? null;
        $ride->pick_up = $data['pick_up'] ?? null;
        $ride->drop_off = $data['drop_off'] ?? null;
        $ride->passenger_name = $data['passenger_name'] ?? null;
        $ride->passenger_email = $data['passenger_email'] ?? null;

//        if ($data['driver']) {
//
//        }

        return $ride;
    }

    public static function convertFromArrayToResponse(array $ride): array
    {
        $response =  [
            'id' => $ride['id'],
            'pick_up' => $ride['pick_up'],
            'status' => $ride['status'],
            'drop_off' => $ride['drop_off'],
        ];

        if (isset($ride['driver'])) {
            $response['driver'] = DriverConverter::convertFromArrayToResponse($ride['driver']);
        }
        return  $response;
    }

    public static function convertFromModelToResponse(Ride $ride): array
    {
        return self::convertFromArrayToResponse($ride->toArray());
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