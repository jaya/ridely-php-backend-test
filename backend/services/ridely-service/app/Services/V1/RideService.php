<?php

namespace App\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RedisStreamsEnum;
use App\Enums\RideEstimateStatusEnum;
use App\Enums\RideStatusEnum;
use App\Exceptions\DriverException;
use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\EstimateRideCriteria;
use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Driver;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\AbstractService;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\Interfaces\RideServiceInterface;
use App\Services\RideCacheService;
use App\Validators\RideValidator;
use Couchbase\QueryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use PHPUnit\Exception;

class RideService extends AbstractService implements RideServiceInterface
{
    private Ride $ride;

    protected ValidationException $exception;
    private RideValidator $validator;
    private LocationServiceInterface $locationService;
    private RideCacheService $rideCacheService;

    public function __construct(Ride $ride, RideValidator $validator, LocationServiceInterface $locationService, RideCacheService $rideCacheService)
    {
        $this->ride = $ride;
        $this->validator = $validator;
        $this->locationService = $locationService;
        $this->rideCacheService = $rideCacheService;
    }

    /**
     * @throws ServiceException|RideException
     */
    public function find($id): Builder|array|Collection|Model
    {
        if ($this->validator->validateId($id)) {
            Log::debug("Searching for the ride with ID: $id");
            return $this->ride->find($id, true);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }

    public function create(CreateRideCriteria $criteria)
    {
        if ($this->validator->validateCreate($criteria)) {
            Log::info("Validation passed for ride creation.");

            $this->checkDatabase();

            $driver = $this->rideCacheService->getNextAvailableDriver();

            if( !$driver) {
                $driver = Driver::getNextAvailableDriver();
            }

            if (!$driver) {
                throw RideException::noDriversAvailable();
            }

            $fields = $criteria->toArray();
            $driverId = $driver->id;

            try {

                if (!$driverId) {
                    throw ServiceException::queryException(ErrorMessagesEnum::INVALID_DRIVER_DATA, []);
                }
                Log::debug("Driver found with ID: {$driverId}");

                Log::debug("==================================================");
                Log::debug("Transaction started for ride creation");
                Log::debug("==================================================");
                DB::beginTransaction();

                Log::debug("Creating ride on the database");
                /**
                 * @var Ride $ride
                 */
                $ride = Ride::create([
                    'passenger_name' => $fields['passenger']['name'],
                    'passenger_email' => $fields['passenger']['email'],
                    'pick_up' => $fields['pick_up'],
                    'drop_off' => $fields['drop_off'],
                    'driver_id' => $driverId
                ]);

                Log::debug("Creating ride-estimate on the database");
                $ride->estimate()->create([
                    'ride_id' => $ride->id,
                    'status' => RideEstimateStatusEnum::PENDING,
                ]);

                // TODO isso quem sabe pode ser migrado para um job para rodar em background
                Log::debug("Requesting a ride");
                $ride->request(true);

                $ride->driver = $driver;
                //$ride->load(['driver', 'estimate']);

                DB::commit();
                Log::debug("==================================================");
                Log::debug("Transaction end - commit");
                Log::debug("==================================================");

            } catch (\Throwable $e) {
                Log::debug("==================================================");
                Log::debug("Transaction end - rollback");
                Log::debug("==================================================");
                DB::rollBack();

                Log::error("Removing the ID [$driverId] from the cache due an error of SQL (Maybe the cached id is not present on the DB)");
                // Remove invalid $driverId from the cache
                $this->rideCacheService->removeDriverFromCache($driverId);

                Log::error($e->getMessage());
                throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_CREATE_RIDE, [], $e);
            }

            Log::debug("Loading ride relationships");
            // associate the driver and estimate to the ride
//            $ride->load(['driver', 'estimate']);

            $this->publishToEstimateRideQueue($ride);

            Log::info('Ride created successfully', ['ride_id' => $ride->id, 'driver_id' => $ride->driver_id]);

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
        Log::info('Publishing ride estimate to Redis stream', ['ride_id' => $ride->id]);

        Redis::xadd(RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value, '*', [
            'ride_id' => $ride->id,
            'estimate_id' => $ride->estimate->id,
            'pick_up' => $ride->pick_up,
            'drop_off' => $ride->drop_off,
            'timestamp' => now()->toIso8601String(),
        ]);

    }

    public function acceptRide($id)
    {
        if ($this->validator->validateId($id)) {

            $this->checkDatabase();

            // TODO incluir transaction
            Log::debug("Searching for the ride with ID: $id");
            $ride = $this->ride->find($id, true);

            try {
                $driver = $this->findDriverOrFail($ride);
            } catch (DriverException $e) {
                Log::error($e->getMessage());
                throw RideException::rideWithoutDriver();
            }


            $ride->accept($driver);
            $this->rideCacheService->removeDriverFromCache($driver->id);

            return $ride;

        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }
    }

    public function cancelRide($id)
    {

        if ($this->validator->validateId($id)) {

            $this->checkDatabase();

            Log::debug("Searching for the ride with ID: $id");
            $ride = $this->ride->find($id);

            $driver = $this->findDriverOrFail($ride);

            $ride->cancel();
            $this->rideCacheService->addDriverToCache($driver);

            return $ride;

        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }
    }

    /**
     * @param Ride $ride
     * @return mixed
     * @throws DriverException
     */
    public function findDriverOrFail(Ride $ride)
    {
        Log::debug("Searching for the driver with ID: $ride->driver_id");
        try {
            $driver = Driver::findOrFail($ride->driver_id);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            throw DriverException::notFound(['driverId' => $ride->driver_id]);
        }
        return $driver;
    }

    public function listRidesWithoutDriver(ListCriteria $criteria): LengthAwarePaginator
    {
        if ($this->validator->validateRead($criteria)) {

            $this->checkDatabase();

            try {

                Log::debug("Listing rides without driver");
                return $this->ride->withoutDriver($criteria);

            } catch (QueryException $e) {
                Log::error($e->getMessage());
                throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_LIST_RIDES, ["criteria" => $criteria->toArray()], $e);
            }
        } else {
            $this->exception = $this->validator->getException();
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    public function delete($id)
    {
        if ($this->validator->validateId($id)) {
            Log::debug("Searching for the ride with ID: $id");
            $ride = $this->ride->find($id);
            $ride->delete();
            return true;
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }
    }

    public function refuseRide($id)
    {
        if ($this->validator->validateId($id)) {

            $this->checkDatabase();

            Log::debug("Searching for the ride with ID: $id");
            $ride = $this->ride->find($id);
            $ride->refuse();
            return $ride;

        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }
    }

    public function finishRide($id)
    {
        if ($this->validator->validateId($id)) {

            $this->checkDatabase();

            Log::debug("Searching for the ride with ID: $id");
            $ride = $this->ride->find($id);
            $ride->finish();
            return $ride;

        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }
    }
}