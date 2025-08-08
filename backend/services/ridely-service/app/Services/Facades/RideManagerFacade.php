<?php

namespace App\Services\Facades;

use App\Exceptions\RideException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Ride;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Services\V1\EstimateRideService;
use Illuminate\Support\Facades\Log;

class RideManagerFacade
{
    protected LocationServiceInterface $locationService;
    private RideServiceInterface $rideService;
    private EstimateRideServiceInterface $estimateRideService;

    public function __construct(RideServiceInterface $rideService, EstimateRideServiceInterface $estimateRideService, LocationServiceInterface $searchLocationService)
    {
        $this->rideService = $rideService;
        $this->locationService = $searchLocationService;
        $this->estimateRideService = $estimateRideService;
    }

    /**
     * @throws RideException
     */
    public function estimateRide(EstimateRideCriteria $criteria, string $id = null): array
    {
        return $this->estimateRideService->estimateRide($criteria, $id);

    }


    public function create(CreateRideCriteria $criteria): Ride
    {
        return $this->rideService->create($criteria);
    }

    public function find(string $id)
    {
        $ride = $this->rideService->find((int)$id);
        $ride->load('estimate'); // Load the estimate relationship
        return $ride;
    }
}