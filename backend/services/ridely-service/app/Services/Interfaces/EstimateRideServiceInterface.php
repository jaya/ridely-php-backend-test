<?php

namespace App\Services\Interfaces;

use App\Enums\RideEstimateStatusEnum;
use App\Http\Criteria\EstimateRideCriteria;
use App\Models\RideEstimate;

interface EstimateRideServiceInterface
{
    public function estimateRide($id, EstimateRideCriteria $criteria): RideEstimate;

    public function updateStatus($id, RideEstimateStatusEnum $estimateStatusEnum): bool;

    public function find($id): ?RideEstimate;
}