<?php

namespace App\Services\V1\Ride;

use App\Exceptions\ServiceException;
use App\Models\Ride;
use App\Services\Interfaces\Ride\RideServiceInterface;
use App\Validators\RideValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RideService implements RideServiceInterface
{
    private Ride $ride;

    protected ValidationException $exception;
    private RideValidator $validator;

    public function __construct(Ride $ride, RideValidator $validator)
    {
        $this->ride = $ride;
        $this->validator = $validator;
    }

    /**
     * @throws ServiceException
     */
    public function getRide(int $id)
    {
        //TODO I can add a cache here
//        Cache::remember()
        Log::debug("Validating ride Id");
        if ($this->validator->validateId($id)) {
            Log::debug("Searching for the rides of the driver");
            return $this->ride->getRideWithDriver($id);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }
}