<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_fare',
        'price_per_km',
        'is_rush_hour',
        'is_flag_2',
    ];

    public static array $fields = [
        'id',
        'name',
        'base_fare',
        'price_per_km',
        'is_rush_hour',
        'is_flag_2',
        'created_at',
        'updated_at'
    ];

    public function filterRuleBasedOnTime($isRushHour, $isFlag2)
    {


        // Select rule based on conditions
        return $this->newQuery()
            ->when($isFlag2, fn($q) => $q->where('is_flag_2', true))
            ->when(!$isFlag2 && $isRushHour, fn($q) => $q->where('is_rush_hour', true))
            ->when(!$isFlag2 && !$isRushHour, fn($q) => $q->where('name', 'default'))
            ->first();

    }
}
