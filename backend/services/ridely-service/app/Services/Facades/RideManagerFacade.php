<?php

namespace App\Services\Facades;

use App\Exceptions\RideException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Http\Hateos\HateosHelper;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Services\V1\EstimateRideService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function cancelRide($id)
    {
        return $this->rideService->cancelRide($id);
    }

    public function listRidesWithoutDriver(ListCriteria $criteria): LengthAwarePaginator
    {
        $paginator = $this->rideService->listRidesWithoutDriver($criteria);
        if (is_array($paginator->items())) {
            $data = $paginator->items();
            $path = $paginator->path();

            $modifiedItems = HateosHelper::addHateosLinksToItems($data, $path);
            return new LengthAwarePaginator(
                $modifiedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }

        return $paginator;
    }

    public function delete(string $id)
    {
        return $this->rideService->delete($id);
    }

    public function refuseRide($id)
    {
        return $this->rideService->refuseRide($id);
    }

    public function finishRide($id)
    {
        return $this->rideService->finishRide($id);
    }
}