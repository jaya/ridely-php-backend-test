<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\Ride\CreateRideCriteria;

interface RideServiceInterface
{

    public function getLocationService(): LocationServiceInterface;
    public function find($id): \Illuminate\Database\Eloquent\Builder|array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model;

    public function create(CreateRideCriteria $criteria);

    public function acceptRide($id);

}