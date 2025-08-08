<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\Ride\CreateRideCriteria;

interface RideServiceInterface
{

    public function getLocationService(): LocationServiceInterface;
    public function find(int $id);

    public function create(CreateRideCriteria $criteria);

}