<?php

namespace App\Converters;

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

}