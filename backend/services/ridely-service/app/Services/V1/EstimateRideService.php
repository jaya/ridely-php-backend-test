<?php

namespace App\Services\V1;

use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\AbstractService;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Validators\EstimateRideValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EstimateRideService extends AbstractService implements EstimateRideServiceInterface
{

    protected ValidationException $exception;
    private EstimateRideValidator $validator;
    private LocationServiceInterface $locationService;

    public function __construct(EstimateRideValidator $validator, LocationServiceInterface $locationService)
    {
        $this->validator = $validator;
        $this->locationService = $locationService;
    }
    public function estimateRide(EstimateRideCriteria $criteria, string $id = null)
    {
        Log::debug('Estimating ride with criteria: ' . json_encode($criteria->toArray()));

        if ($this->validator->validate($criteria, $id)) {
            Log::debug("Searching for ride with ID: $id");
            $estimate = $this->find($id);

            if (!$estimate) {
                throw RideException::estimateNotFound();
            }


            $estimate->processing();
            Log::info("Ride estimate processing estimate ID: {$estimate->id}, status changed to PROCESSING");

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

            Log::debug(sprintf(
                "Estimated ride %d: distance %s km, duration %s min, price %s",
                $id, round($distanceKm, 1), $durationMin, $price
            ));

            $estimate->ready($distanceKm, $durationMin, $price);

            return [
                'distance_km' => round($distanceKm, 1),
                'duration_min' => $durationMin,
                'price_estimate' => $price,
            ];
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    private function getCoordinatesFromAddress(string $address, $wait = false)
    {
        return $this->locationService->execute($address, $wait);
    }

    public function updateEstimateRide(mixed $estimateId, \App\Enums\RideEstimateStatusEnum $estimateStatusEnum)
    {
        $estimate = $this->find($estimateId);
        $estimate->updateStatus($estimateStatusEnum);
    }

    public function find($id): RideEstimate
    {
        return RideEstimate::findOrFail($id);
    }
}