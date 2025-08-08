<?php

namespace App\Services\Interfaces;

use App\Http\Criteria\EstimateRideCriteria;

interface EstimateRideServiceInterface
{
    public function estimateRide(EstimateRideCriteria $criteria, string $id = null);

    public function updateEstimateRide(mixed $estimateId, \App\Enums\RideEstimateStatusEnum $estimateStatusEnum);

    public function find($id): \App\Models\RideEstimate;
}