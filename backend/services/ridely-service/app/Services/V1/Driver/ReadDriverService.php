<?php

namespace App\Services\V1\Driver;

use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Repositories\V1\DriverRepository;
use App\Services\Interfaces\Driver\ReadDriverServiceInterface;
use App\Validators\DriverValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReadDriverService implements ReadDriverServiceInterface
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverRepository $repository,
        protected DriverValidator $validator
    ) {}

    /**
     * @throws ServiceException
     * @throws RepositoryException
     */
    public function execute(Criteria $criteria): LengthAwarePaginator
    {
        Log::debug("Validating criteria");
        if ($this->validate($criteria)) {
            Log::debug("Searching for drivers");
            return $this->repository->all($criteria);
        } else {
            Log::error(sprintf("Validation error: %s", $this->exception->getMessage()));
            throw ServiceException::invalidRequestParam($this->exception->getMessage(), $criteria->toArray(), $this->exception);
        }
    }

    /**
     * @throws RepositoryException
     * @throws ServiceException
     */
    public function count(Criteria $criteria): int
    {
        Log::debug("Validating criteria");
        if ($this->validate($criteria)) {
            Log::debug("Counting for drivers");
            return $this->repository->count($criteria);
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