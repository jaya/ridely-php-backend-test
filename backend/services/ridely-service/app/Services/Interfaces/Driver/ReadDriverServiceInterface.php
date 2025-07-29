<?php

namespace App\Services\Interfaces\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Repositories\V1\DriverRepository;
use App\Validators\DriverValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

interface ReadDriverServiceInterface
{


    public function execute(Criteria $criteria):LengthAwarePaginator;

    public function count(Criteria $criteria);

    public function validate(Criteria $criteria): bool;


}