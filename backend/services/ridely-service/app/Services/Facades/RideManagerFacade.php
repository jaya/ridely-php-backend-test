<?php

namespace App\Services\Facades;

use App\Exceptions\RideException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Services\Interfaces\Location\LocationServiceInterface;

class RideManagerFacade
{
    protected LocationServiceInterface $locationService;

    public function __construct(LocationServiceInterface $searchLocationService)
    {
        $this->locationService = $searchLocationService;
    }

    /**
     * @throws RideException
     */
    public function estimateRide(EstimateRideCriteria $criteria): array
    {
        $origin = $this->getCoordinatesFromAddress($criteria->getPickUp());
        $destination = $this->getCoordinatesFromAddress($criteria->getDropOff(), true);

        if (!$origin || !$destination) {
            throw RideException::unableToLocateAddressData();
        }

        $distanceKm = $this->locationService->calculateArea(
            $origin['lat'], $origin['lon'],
            $destination['lat'], $destination['lon']
        );

        $durationMin = $this->locationService->calculateDurationTime($distanceKm);

        $price = $this->locationService->calculatePrice($distanceKm);

        return [
            'distance_km' => round($distanceKm, 1),
            'duration_min' => $durationMin,
            'price_estimate' => $price,
        ];
    }

    private function getCoordinatesFromAddress(string $address, $wait = false)
    {
        return $this->locationService->execute($address, $wait);
    }
}