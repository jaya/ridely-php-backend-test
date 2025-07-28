<?php

namespace App\Services\Interfaces\Driver;

use App\Exceptions\ServiceException;
use App\Repositories\V1\DriverRepository;
use App\Validator\DriverValidator;
use Illuminate\Validation\ValidationException;

interface CreateDriver
{


    public function execute(array $data);


    public function validate($data): bool;
}