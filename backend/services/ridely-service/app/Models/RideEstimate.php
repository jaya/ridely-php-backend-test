<?php

namespace App\Models;

use App\Enums\RideEstimateStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'distance_km',
        'duration_min',
        'price_estimate',
    ];

    public static array $fields = [
        'id',
        'distance_km',
        'duration_min',
        'price_estimate',
        'created_at',
        'updated_at'
    ];

    protected $casts = ['status' => RideEstimateStatusEnum::class];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function processing()
    {

        $this->status = RideEstimateStatusEnum::PROCESSING;
        $this->save();
    }

    public function ready($distanceKm, $durationMin, $price)
    {
        $this->distance_km = $distanceKm;
        $this->duration_min = $durationMin;
        $this->price_estimate = $price;
        $this->status = RideEstimateStatusEnum::READY;
        $this->save();
    }

    public function updateStatus(RideEstimateStatusEnum $status)
    {
        $this->status = $status;
        $this->save();
    }
}
