<?php

namespace App\Services\V2\Driver;

use App\Exceptions\ServiceException;
use App\Repositories\V1\DriverRepository;
use App\Validator\DriverValidator;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\Driver\CreateDriver;

// Note: Teste para futuras versões do serviço
// TODO implementar uma mudança para demonstrar o uso de uma v2
class CreateDriverService implements CreateDriver
{
    protected ValidationException $exception;

    public function __construct(
        protected DriverRepository $repository,
        protected DriverValidator $validator
    ) {}

    public function execute(array $data)
    {
        if ($this->validate($data)) {
            return $this->repository->create($data);
        } else {
            throw ServiceException::invalidRequest($this->exception->getMessage());
        }

    }

    public function validate($data): bool {
        $result = $this->validator->validateCreate($data);
        if (!$result) {
            $this->exception = $this->validator->getException();
        }

        return  $result;
    }
}