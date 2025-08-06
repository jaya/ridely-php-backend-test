<?php

namespace App\Services\Interfaces\Location;

interface LocationServiceInterface
{


    public function execute(string $address, bool $wait = false);

    public function validate(string $address): bool;

    public function calculateArea($lat1, $lon1, $lat2, $lon2): float;

    public function calculateDurationTime($distanceKm): int;

    public function calculatePrice($distanceKm): float;
}