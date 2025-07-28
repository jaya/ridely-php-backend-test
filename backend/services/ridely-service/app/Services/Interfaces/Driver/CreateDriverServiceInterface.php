<?php

namespace App\Services\Interfaces\Driver;

use App\Exceptions\ServiceException;
use App\Repositories\V1\DriverRepository;
use App\Validators\DriverValidator;
use Illuminate\Validation\ValidationException;

interface CreateDriverServiceInterface
{


    public function execute(array $data);


    public function validate($data): bool;
}