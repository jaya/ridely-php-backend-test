<?php

namespace App\Repositories\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\RepositoryException;
use App\Http\Criteria\Criteria;
use App\Models\Driver;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DriverRepository
{
    /**
     * @throws RepositoryException
     */
    public function create(array $data): Driver
    {
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
            throw new RepositoryException(message: ErrorMessagesEnum::UNABLE_TO_CREATE_DRIVER->value);
        }
    }

    public function update(int $id, array $data): Driver
    {
        try {
            $driver = Driver::findOrFail($id); // Throws ModelNotFoundException if not found
            $driver->update($data);
            return $driver;
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found with ID: $id");
            throw new RepositoryException(message: ErrorMessagesEnum::DRIVER_NOT_FOUND->value);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new RepositoryException(message: ErrorMessagesEnum::UNABLE_TO_UPDATE_DRIVER->value);
        }
    }


    public function delete(int $id): void
    {
        try {
            $deleted = Driver::destroy($id);

            // If no rows were affected, it means the driver wasn't found
            if ($deleted === 0) {
                throw new ModelNotFoundException();
            }
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found for deletion with ID: $id");
            throw new RepositoryException(message: ErrorMessagesEnum::DRIVER_NOT_FOUND->value);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new RepositoryException(message: ErrorMessagesEnum::UNABLE_TO_DELETE_DRIVER->value);
        }
    }

    public function find(int $id): Driver
    {
        try {
            return Driver::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::warning("Driver not found with ID: $id");
            throw new RepositoryException(message: ErrorMessagesEnum::DRIVER_NOT_FOUND->value);
        }
    }

    public function all(Criteria $criteria): array
    {
        try {
            //return Driver::all()->toArray();
            $query = Driver::query();
            // Filters and orders
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
            throw new RepositoryException(message: ErrorMessagesEnum::UNABLE_TO_LIST_DRIVERS->value);
        }
    }
}