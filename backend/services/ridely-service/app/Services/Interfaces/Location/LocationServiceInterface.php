<?php

namespace App\Services\Interfaces\Location;

use App\Exceptions\ServiceException;
use App\Http\Criteria\ListCriteria;
use App\Repositories\V1\DriverRepository;
use App\Validators\DriverValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

interface LocationServiceInterface
{


    public function execute(string $address, bool $wait = false);

    public function validate(string $address): bool;

    public function calculateArea($lat1, $lon1, $lat2, $lon2): float;

    public function calculateDurationTime($distanceKm): int;

    public function calculatePrice($distanceKm): float;
}