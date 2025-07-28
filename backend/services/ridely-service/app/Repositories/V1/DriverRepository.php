<?php

namespace App\Repositories\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\RepositoryException;
use App\Http\Criteria\Criteria;
use App\Models\Driver;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverRepository
{
    /**
     * @throws RepositoryException
     */
    public function checkIfDatabaseConnectionIsAvailable(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw RepositoryException::databaseTemporarilyUnavailable($e->getMessage(), $e);
        }
    }
    /**
     * @throws RepositoryException
     */
    public function create(array $data): Driver
    {
        $this->checkIfDatabaseConnectionIsAvailable();

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
            throw RepositoryException::queryException(ErrorMessagesEnum::UNABLE_TO_CREATE_DRIVER, [], $e);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, array $data): Driver
    {
        $this->checkIfDatabaseConnectionIsAvailable();

        try {
            $driver = Driver::findOrFail($id); // Throws ModelNotFoundException if not found
            $driver->update($data);
            return $driver;
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found with ID: $id");
            throw RepositoryException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw RepositoryException::queryException(ErrorMessagesEnum::UNABLE_TO_UPDATE_DRIVER, ["id" => $id], $e);
        }
    }


    /**
     * @throws RepositoryException
     */
    public function delete(int $id): void
    {
        try {

            $this->checkIfDatabaseConnectionIsAvailable();

            $deleted = Driver::destroy($id);

            // If no rows were affected, it means the driver wasn't found
            if ($deleted === 0) {
                throw new ModelNotFoundException();
            }
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found for deletion with ID: $id");
            throw RepositoryException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw RepositoryException::queryException(ErrorMessagesEnum::UNABLE_TO_DELETE_DRIVER, ["id" => $id], $e);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function find(int $id): Driver
    {
        $this->checkIfDatabaseConnectionIsAvailable();

        try {
            return Driver::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found with ID: $id");
            throw RepositoryException::notFound(ErrorMessagesEnum::DRIVER_NOT_FOUND, ["id" => $id], $e);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function all(Criteria $criteria): array
    {
        $this->checkIfDatabaseConnectionIsAvailable();

        try {
            $query = Driver::query();
            // Previous validated
            if ($criteria->fields) {
                $query->select($criteria->fields);
            }

            $query->orderBy($criteria->orderBy, $criteria->sortBy);

            if ($criteria->offset !== null) {
                $query->offset($criteria->offset);
            }

            if ($criteria->limit !== null) {
                $query->limit($criteria->limit);
            }

            return $query->get()->toArray();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw RepositoryException::queryException(ErrorMessagesEnum::UNABLE_TO_LIST_DRIVERS, ["criteria" => $criteria->toArray()], $e);
        }
    }
}