<?php

namespace App\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\DriverException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Driver;
use App\Services\AbstractService;
use App\Services\DriverCacheService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Validators\DriverValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DriverService extends AbstractService implements DriverServiceInterface
{
    protected ValidationException $exception;
    protected DriverValidator $validator;
    private DriverCacheService $driverCacheService;

    public function __construct(
        Driver $driver,
        DriverValidator $validator,
        DriverCacheService $driverCacheService
    )
    {
        $this->driverModel = $driver;
        $this->validator = $validator;
        $this->driverCacheService = $driverCacheService;
    }

    public function create(CreateDriverCriteria $criteria): Driver
    {

        if ($this->validator->validateCreate($criteria)) {

            $this->checkDatabase();

            $data = $criteria->toArray();
            return $this->createDriver($data);
        } else {
            $this->exception = $this->validator->getException();
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequest($this->exception->getMessage(), []);
        }
    }

    public function read(ListCriteria $criteria): LengthAwarePaginator
    {
        if ($this->validator->validateRead($criteria)) {

            $this->checkDatabase();

            try {
                return $this->driverModel->allDrivers($criteria);
            } catch (QueryException $e) {
                Log::error($e->getMessage());
                throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_LIST_DRIVERS, ["criteria" => $criteria->toArray()], $e);
            }
        } else {
            $this->exception = $this->validator->getException();
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    public function update(): Driver
    {
        throw ServiceException::notImplemented();
    }

    public function softDelete($id): bool
    {
        throw ServiceException::notImplemented();
    }

    /**
     * @throws ServiceException
     * @throws DriverException
     */
    public function delete($id): bool
    {
        if ($this->validator->validateDelete($id)) {
            try {
                $driver = $this->driverModel->findOrFail($id);
                if (!$driver) {
                    throw new ModelNotFoundException();
                }
                $deleted = $driver->delete();

                if (!$deleted) {
                    throw new ModelNotFoundException();
                }
            } catch (ModelNotFoundException $e) {
                Log::warning("Driver not found for deletion with ID: $id");
                throw DriverException::notFound(["id" => $id]);
            } catch (QueryException $e) {
                Log::error($e->getMessage());
                throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_DELETE_DRIVER, ["id" => $id], $e);
            }
        } else {
            $this->exception = $this->validator->getException();
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['id' => $id], $this->exception);
        }

        return true;

    }

    public function find($id): Driver
    {
        if ($this->validator->validateId($id)) {
            Log::debug("Searching for the driver with ID: $id");
            try {
                return Driver::findOrFail($id);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                throw DriverException::notFound();
            }
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }

    public function getOpenRides($id, ListCriteria $criteria): LengthAwarePaginator
    {
        if ($this->validator->validateId($id) && $this->validator->validateRead($criteria)) {

            $this->checkDatabase();

            Log::debug("Searching for the driver with ID: $id");
            /**
             * @var Driver $driver
             */
            $driver = null;
            try {
                $driver = Driver::findOrFail($id);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                throw DriverException::notFound();
            }

            $paginator = $driver->getOpenRides($criteria);

            if ($paginator->isEmpty()) {
                throw DriverException::noRidesWaitingToBeAccepted();
            }

            return $paginator;
//
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), ['rideId' => $id], $this->exception);
        }

    }

    /**
     * @param array $data
     * @return mixed
     * @throws ServiceException
     */
    private function createDriver(array $data)
    {
        try {
            Log::debug("Creating driver on the database");
            return $this->driverModel->create([
                'name' => $data['name'],
                'car_license_plate' => $data['car']['license_plate'],
                'car_model' => $data['car']['model'],
                'car_color' => $data['car']['color'],
                'available' => $data['available'] ?? true,
            ]);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_CREATE_DRIVER, [], $e);
        }
    }
}