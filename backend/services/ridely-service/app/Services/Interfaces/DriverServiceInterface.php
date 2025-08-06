<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverServiceInterface
{

    public function create(array $data): Driver;

    public function read(ListCriteria $criteria): LengthAwarePaginator;

    public function update();

    public function delete();
}