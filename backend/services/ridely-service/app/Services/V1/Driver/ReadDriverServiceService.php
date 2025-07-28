<?php

namespace App\Services\V1\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Repositories\V1\DriverRepository;
use App\Services\Interfaces\Driver\ReadDriverService;
use App\Validators\DriverValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReadDriverServiceService implements ReadDriverService
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverRepository $repository,
        protected DriverValidator $validator
    ) {}

    public function execute(Criteria $criteria)
    {
        if ($this->validate($criteria)) {
            Log::debug("Searching for drivers");
            return $this->repository->all($criteria);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    public function validate(Criteria $criteria): bool {
        $result = $this->validator->validateRead($criteria);
        if (!$result) {
            $this->exception = $this->validator->getException();
        }

        return  $result;
    }

    public function getException(): ValidationException
    {
        return $this->exception;
    }
}