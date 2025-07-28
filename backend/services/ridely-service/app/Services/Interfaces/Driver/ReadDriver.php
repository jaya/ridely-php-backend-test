<?php

namespace App\Services\Interfaces\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Repositories\V1\DriverRepository;
use App\Validator\DriverValidator;
use Illuminate\Validation\ValidationException;

interface ReadDriver
{


    public function execute(Criteria $criteria);


    public function validate(Criteria $criteria): bool;
}