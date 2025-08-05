<?php

namespace App\Services\Interfaces\Driver;

use App\Exceptions\ServiceException;
use App\Http\Criteria\ListCriteria;
use App\Repositories\V1\DriverRepository;
use App\Validators\DriverValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

interface ReadDriverServiceInterface
{


    public function execute(ListCriteria $criteria):LengthAwarePaginator;

    public function count(ListCriteria $criteria);

    public function validate(ListCriteria $criteria): bool;


}