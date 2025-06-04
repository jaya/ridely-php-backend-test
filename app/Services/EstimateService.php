<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EstimateService
{
    protected const BASE_RATE = 5.00;
    protected const COST_PER_KM = 2.50;
    protected const COST_PER_MINUTE = 0.50;

    public function calculate(int $distanceKm, int $timeInMinutes)
    {
        $price = self::BASE_RATE + ($distanceKm * self::COST_PER_KM) + ($timeInMinutes * self::COST_PER_MINUTE);
    
        Log::info('Estimate calculated ', [
            'id' => Str::uuid()->toString(),
            'price' => $price
        ]);

        return $price;
    }
}
