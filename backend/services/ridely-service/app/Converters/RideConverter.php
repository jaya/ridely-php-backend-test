<?php

namespace App\Converters;

use App\Models\Ride;

class RideConverter
{
    public static function convertFromArrayToResponse(array $ride): array
    {
        $response =  [
            'id' => $ride['id'],
            'status' => $ride['status'],
            'pick_up' => $ride['pick_up'],
            'drop_off' => $ride['drop_off'],
        ];

        if (isset($ride['driver'])) {
            $response['driver'] = DriverConverter::convertFromArrayToResponse($ride['driver']);
            unset($response['driver']['id']);
        }

        if (isset($ride['estimate'])) {
            $response['estimate'] = $ride['estimate'];
            unset($response['estimate']['id']);
            unset($response['estimate']['ride_id']);
            unset($response['estimate']['created_at']);
            unset($response['estimate']['updated_at']);
        }

        return  $response;
    }

    public static function convertFromModelToResponse(Ride $ride): array
    {
        return self::convertFromArrayToResponse($ride->toArray());
    }

}