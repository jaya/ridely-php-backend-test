<?php

namespace App\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RedisStreamsEnum;
use App\Enums\RideEstimateStatusEnum;
use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Driver;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\AbstractService;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Validators\RideValidator;
use Couchbase\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class RideService extends AbstractService implements RideServiceInterface
{
    private Ride $ride;

    protected ValidationException $exception;
    private RideValidator $validator;
    private LocationServiceInterface $locationService;

    public function __construct(Ride $ride, RideValidator $validator, LocationServiceInterface $locationService)
    {
        $this->ride = $ride;
        $this->validator = $validator;
        $this->locationService = $locationService;
    }

    /**
     * @throws ServiceException|RideException
     */
    public function find($id): Builder|array|Collection|Model
    {
        if ($this->validator->validateId($id)) {
            Log::debug("Searching for the ride with ID: $id");
            return $this->ride->find($id);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }

    public function create(CreateRideCriteria $criteria)
    {
        if ($this->validator->validateCreate($criteria)) {

            $this->checkDatabase();

            $driver = Driver::where('available', true)
                ->orderBy('activation_date', 'asc')
                ->first();

            if (!$driver) {
                throw RideException::noDriversAvailable();
            }

            $fields = $criteria->toArray();

            try {
                $ride = Ride::create([
                    'passenger_name' => $fields['passenger']['name'],
                    'passenger_email' => $fields['passenger']['email'],
                    'pick_up' => $fields['pick_up'],
                    'drop_off' => $fields['drop_off'],
                    'driver_id' => $driver->id
                ]);

                $estimate = RideEstimate::create([
                    'ride_id' => $ride->id,
                    'status' => RideEstimateStatusEnum::PENDING,
                ]);

                $ride->estimate()->save($estimate);


            } catch (QueryException $e) {
                Log::error($e->getMessage());
                throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_CREATE_RIDE, [], $e);
            }

            $ride->request();

            // associate the driver and estimate to the ride
            $ride->load(['driver', 'estimate']);

            $this->publishToEstimateRideQueue($ride);

            return $ride;

        } else {
            $this->exception = $this->validator->getException();
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequest($this->exception->getMessage(), []);
        }
    }

    public function getLocationService(): LocationServiceInterface
    {
        return $this->locationService;
    }

    private function publishToEstimateRideQueue($ride)
    {
        // Envia para o Redis Stream
        Redis::xadd(RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value, '*', [
            'ride_id' => $ride->id,
            'estimate_id' => $ride->estimate->id,
            'pick_up' => $ride->pick_up,
            'drop_off' => $ride->drop_off,
            'timestamp' => now()->toIso8601String(),
        ]);

    }
}