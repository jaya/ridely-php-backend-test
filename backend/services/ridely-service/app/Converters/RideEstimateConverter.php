<?php

namespace App\Converters;

use App\Enums\RideStatusEnum;
use App\Models\Ride;
use App\Models\RideEstimate;

class RideEstimateConverter
{
    public static function convertFromArrayToResponse(array $estimateRide): array
    {
        return [
            'status' => $estimateRide['status'],
            'distance_km' => round($estimateRide['distance_km'],1),
            'duration_min' => $estimateRide['duration_min'],
            'price_estimate' => $estimateRide['price_estimate'],
        ];
    }

    public static function convertFromModelToResponse(RideEstimate $ride): array
    {
        return self::convertFromArrayToResponse($ride->toArray());
    }

    public static function convertFromArrayToModel($data)
    {
        $rideEstimate = new RideEstimate();
        $rideEstimate->id = $data['id'] ?? null;
        $rideEstimate->distance_km = $data['distance_km'] ?? null;
        $rideEstimate->duration_min = $data['duration_min'] ?? null;
        $rideEstimate->price_estimate = $data['price_estimate'] ?? null;
        $rideEstimate->created_at = $data['created_at'] ?? null;
        $rideEstimate->updated_at = $data['updated_at'] ?? null;


        return $rideEstimate;
    }

}