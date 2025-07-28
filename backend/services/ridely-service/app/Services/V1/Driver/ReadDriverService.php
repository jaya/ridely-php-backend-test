<?php

namespace App\Services\V1\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Repositories\V1\DriverRepository;
use App\Services\Interfaces\Driver\ReadDriver;
use App\Validator\DriverValidator;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\Driver\CreateDriver;

class ReadDriverService implements ReadDriver
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverRepository $repository,
        protected DriverValidator $validator
    ) {}

    public function execute(Criteria $criteria)
    {
        if ($this->validate($criteria)) {
            return $this->repository->all($criteria);
        } else {
            throw ServiceException::invalidRequestParam($this->exception->getMessage());
        }
    }

    public function validate(Criteria $criteria): bool {
        $result = $this->validator->validateRead($criteria);
        if (!$result) {
            $this->exception = $this->validator->getException();
        }

        return  $result;
    }
}