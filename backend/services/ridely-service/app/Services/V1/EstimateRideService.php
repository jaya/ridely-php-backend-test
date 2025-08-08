<?php

namespace App\Services\V1;

use App\Enums\RideEstimateStatusEnum;
use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Models\RideEstimate;
use App\Services\AbstractService;
use App\Services\Interfaces\EstimateRideServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
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

    /**
     * It keeps the criteria to simplify the lecture in the Redis queue, just ids will not be easy to read
     * @param $id
     * @param EstimateRideCriteria $criteria
     * @return RideEstimate
     * @throws RideException
     * @throws ServiceException
     */
    public function estimateRide($id, EstimateRideCriteria $criteria): RideEstimate
    {
        Log::info('Estimating ride with criteria: ' . json_encode($criteria->toArray()));

        if ($this->validator->validate($id, $criteria)) {
            Log::debug("Searching for estimateRide with ID: $id");
            $estimate = $this->find($id);

            if (!$estimate) {
                throw RideException::estimateNotFound();
            }

            $estimate->processing();
            Log::info("Status changed to PROCESSING");

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
            $rideId = $estimate->ride_id;

            Log::info(sprintf(
                "Estimated ride %d: distance %s km, duration %s min, price %s",
                $rideId, round($distanceKm, 1), $durationMin, $price
            ));

            $estimate->ready($distanceKm, $durationMin, $price);

            return $estimate;
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    private function getCoordinatesFromAddress(string $address, $wait = false)
    {
        return $this->locationService->getCoordinatesFromAddress($address, $wait);
    }

    public function updateStatus($id, RideEstimateStatusEnum $estimateStatusEnum): bool
    {
        $estimate = $this->find($id);
        $estimate->updateStatus($estimateStatusEnum);
        return ($estimate->status == $estimateStatusEnum);
    }

    public function find($id): RideEstimate
    {
        if ($this->validator->validateId($id)) {
            return RideEstimate::findOrFail($id);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }
}