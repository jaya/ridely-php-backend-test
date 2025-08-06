<?php

namespace App\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ServiceException;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Services\AbstractService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Validators\DriverValidator;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DriverService extends AbstractService implements DriverServiceInterface
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverValidator $validator
    ) {}

    public function create(array $data): Driver
    {

        if ($this->validator->validateCreate($data)) {

            $this->checkDatabase();

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

    public function update()
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

    public function delete()
    {
        throw ServiceException::notImplemented();
        // TODO: Implement delete() method.
//        try {
//
//
//            $deleted = Driver::destroy($id);
//
//            // If no rows were affected, it means the driver wasn't found
//            if ($deleted === 0) {
//                throw new ModelNotFoundException();
//            }
//        } catch (ModelNotFoundException $e) {
//            Log::warning("Driver not found for deletion with ID: $id");
//            throw ServiceException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
//        } catch (QueryException $e) {
//            Log::error($e->getMessage());
//            throw ServiceException::queryException(ErrorMessagesEnum::UNABLE_TO_DELETE_DRIVER, ["id" => $id], $e);
//        }
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