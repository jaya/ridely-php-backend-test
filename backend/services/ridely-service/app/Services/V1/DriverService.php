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

    public function __construct(
        DriverValidator $validator
    )
    {
        $this->validator = $validator;
    }

    public function create(CreateDriverCriteria $criteria): Driver
    {

        if ($this->validator->validateCreate($criteria)) {

            $this->checkDatabase();

            $data = $criteria->toArray();
            try {
                return Driver::create([
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
                return Driver::allDrivers($criteria);
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
        // TODO: Implement update() method.
//        try {
//            $driver = Driver::findOrFail($id); // Throws ModelNotFoundException if not found
//            $driver->update($data);
//            return $driver;
//        } catch (ModelNotFoundException $e) {
//            Log::warning("Driver not found with ID: $id");
//            throw ServiceException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
//        } catch (QueryException $e) {
//            Log::error($e->getMessage());
//            throw RepositoryException::queryException(ErrorMessagesEnum::UNABLE_TO_UPDATE_DRIVER, ["id" => $id], $e);
//        }
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
                $driver = Driver::findOrFail($id);
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

    public function find(int $id): Driver
    {
        throw ServiceException::notImplemented();

//        try {
//            return Driver::findOrFail($id);
//        } catch (ModelNotFoundException $e) {
//            Log::warning("Driver not found with ID: $id");
//            throw ServiceException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
//        }
    }

//    public function count(ListCriteria $criteria): int
//    {
//
//        try {
//            $query = Driver::query();
//
//            $query->select(DB::raw('count(*) as count'));
//
//            $query->orderBy($criteria->orderBy, $criteria->sortBy);
//
//            Log::debug(sprintf("Count query: %s", $query->toSql()));
//            return $query->count();
//        } catch (QueryException $e) {
//            Log::error($e->getMessage());
//            throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_LIST_DRIVERS, ["criteria" => $criteria->toArray()], $e);
//        }
//    }
}