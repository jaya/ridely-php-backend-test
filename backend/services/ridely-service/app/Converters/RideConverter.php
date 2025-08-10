<?php

namespace App\Converters;

use App\Enums\RideStatusEnum;
use App\Models\Driver;
use App\Models\Ride;

class RideConverter
{
    public static function convertFromArrayToModel(array $data)
    {
        $ride = new Ride();
        $ride->id = $data['id'] ?? null;
        $ride->driver_id = $data['driver_id'] ?? null;
        $ride->pick_up = $data['pick_up'] ?? null;
        $ride->drop_off = $data['drop_off'] ?? null;

        if (isset($data['passenger'])) {
            $ride->passenger_name = $data['passenger']['name'] ?? null;
            $ride->passenger_email = $data['passenger']['email'] ?? null;
        }
        if($data['status']){
            $ride->status =  RideStatusEnum::tryFrom($data['status']);
        }

        $ride->created_at = $data['created_at'] ?? null;
        $ride->updated_at = $data['updated_at'] ?? null;


        return $ride;
    }

    public static function convertFromArrayToResponse(array $ride): array
    {
        $response =  [
            'id' => $ride['id'],
            'status' => $ride['status'],
            'pick_up' => $ride['pick_up'],
            'drop_off' => $ride['drop_off'],
        ];

        if (isset($ride['driver'])) {
            $driverData = $ride['driver'];
            if (!is_array($ride['driver'])) {
                $driverData = $ride['driver']->toArray();
            }
            $response['driver'] = DriverConverter::convertFromArrayToResponse($driverData);
            unset($response['driver']['id']);
        }

        if (isset($ride['estimate'])) {
            $response['estimate'] = $ride['estimate'];
            unset($response['estimate']['id']);
            unset($response['estimate']['ride_id']);
            unset($response['estimate']['created_at']);
            unset($response['estimate']['updated_at']);
        }

        if (isset($ride['passenger'])) {
            $response['passenger'] = [
                'name' => $ride['passenger']['name'] ?? null,
                'email' => $ride['passenger']['email'] ?? null
            ];
        } else {
            $response['passenger'] = [
                'name' => $ride['passenger_name'] ?? null,
                'email' => $ride['passenger_email'] ?? null
            ];
        }

        if (isset($ride['_links'])) {
            $response['_links'] = $ride['_links'];
        }

        return  $response;
    }

    public static function convertListFromArrayToResponse($items): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = self::convertFromArrayToResponse($item);
        }
        return $result;
    }

}