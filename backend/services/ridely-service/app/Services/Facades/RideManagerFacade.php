<?php

namespace App\Services\Facades;

use App\Exceptions\RideException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Services\V1\EstimateRideService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
     * @param string|null $id Ride ID
     */
    public function estimateRide(string $id = null): RideEstimate
    {
        $ride = $this->find($id);
        if (!$ride) {
            RideException::notFound();
        }
        $estimateId = $ride->estimate->id;
        $criteria = new EstimateRideCriteria(
            [
                "pick_up" => $ride->pick_up,
                "drop_off" => $ride->drop_off
            ]
        );
        return $this->estimateRideService->estimateRide($estimateId, $criteria);

    }


    public function create(CreateRideCriteria $criteria): Ride
    {
        return $this->rideService->create($criteria);
    }

    public function find(string $id): array|Builder|Collection|Model
    {
        $ride = $this->rideService->find($id);
        $ride->load('estimate');
        return $ride;
    }

    public function findEstimateRideByRideId($id)
    {
        return $this->estimateRideService->find($id);
    }

    public function acceptRide($id)
    {
        return $this->rideService->acceptRide($id);
    }
}