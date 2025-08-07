<?php

namespace App\Services\V2;

use App\Exceptions\ServiceException;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Services\AbstractService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Validators\DriverValidator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class V2DriverService extends AbstractService implements DriverServiceInterface
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverValidator $validator
    ) {}

    public function create(CreateDriverCriteria $criteria): Driver
    {
        throw ServiceException::notImplemented();
    }

    public function read(ListCriteria $criteria): LengthAwarePaginator
    {
        throw ServiceException::notImplemented();
    }

    public function update(): Driver
    {
        throw ServiceException::notImplemented();
    }

    public function delete($id): bool
    {
        throw ServiceException::notImplemented();
    }

    public function softDelete($id): bool
    {
        throw ServiceException::notImplemented();
    }
}