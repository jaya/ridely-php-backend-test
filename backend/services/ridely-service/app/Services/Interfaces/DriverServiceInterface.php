<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverServiceInterface
{

    public function create(CreateDriverCriteria $criteria): Driver;

    public function read(ListCriteria $criteria): LengthAwarePaginator;

    public function update(): Driver;

    public function delete($id): bool;

    public function softDelete($id): bool;
}