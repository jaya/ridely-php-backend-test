<?php

namespace App\Services\V2\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Services\AbstractService;
use App\Services\Interfaces\Driver\DriverServiceInterface;
use App\Validators\DriverValidator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class V2DriverService extends AbstractService implements DriverServiceInterface
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverValidator $validator
    ) {}

    public function create(array $data): Driver
    {
        throw ServiceException::notImplemented();
    }

    public function read(ListCriteria $criteria): LengthAwarePaginator
    {
        throw ServiceException::notImplemented();
    }

    public function update()
    {
        throw ServiceException::notImplemented();
    }

    public function delete()
    {
        throw ServiceException::notImplemented();
    }
}