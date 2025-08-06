<?php

namespace App\Services\Facades;

use App\Exceptions\RideException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;

class RideManagerFacade
{
    protected LocationServiceInterface $locationService;
    private RideServiceInterface $rideService;

    public function __construct(RideServiceInterface $rideService, LocationServiceInterface $searchLocationService)
    {
        $this->rideService = $rideService;
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

    public function getRide(string $id)
    {
        return $this->rideService->getRide((int)$id);
    }
}