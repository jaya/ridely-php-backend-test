<?php

namespace App\Services\Interfaces\Driver;

use App\Http\Criteria\ListCriteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReadDriverServiceInterface
{


    public function execute(ListCriteria $criteria):LengthAwarePaginator;

    public function count(ListCriteria $criteria);

    public function validate(ListCriteria $criteria): bool;


}