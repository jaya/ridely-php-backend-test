<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RideServiceInterface
{

    public function getLocationService(): LocationServiceInterface;

    public function find($id): Builder|array|Collection|Model;

    public function create(CreateRideCriteria $criteria);

    public function acceptRide($id);

    public function cancelRide($id);

    public function listRidesWithoutDriver(ListCriteria $criteria): LengthAwarePaginator;

    public function delete($id);

    public function refuseRide($id);

    public function finishRide($id);

}