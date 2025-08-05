<?php

namespace App\Services\V1\Location;

use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use App\Models\PricingRule;
use App\Services\Interfaces\Location\LocationServiceInterface;
use App\Validators\LocationValidator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LocationService implements LocationServiceInterface
{
    const EARTH_RADIUS = 6371; // km

    private string $locationServiceUrl;

    protected ValidationException $exception;

    protected LocationValidator $validator;

    public function __construct(
        LocationValidator $validator,
        string $locationServiceUrl = null)
    {
        if (empty($locationServiceUrl)) {
            $locationServiceUrl = env('LOCATION_SERVICE_URL', 'https://nominatim.openstreetmap.org/search');
        }

        $this->locationServiceUrl = $locationServiceUrl;
        $this->validator = $validator;

    }
    public function execute(string $address): ?array
    {

        $response = Http::withHeaders([
            'User-Agent' => 'RidelyApp/1.0 (www.ridely.com.br)'
        ])->get($this->locationServiceUrl, [
            'format' => 'jsonv2',
            'q' => $address,
        ]);

        $data = $response->json();

        if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            return null;
        }

        $locationData = $data[0];

        return [
            'lat' => (float) $locationData['lat'],
            'lon' => (float) $locationData['lon'],
        ];

    }

    public function validate(string $address): bool
    {
        $result = $this->validator->validate($address);
        if (!$result) {
            $this->exception = $this->validator->getException();
        }

        return  $result;
    }

    public function getException(): ValidationException
    {
        return $this->exception;
    }


    public function calculateArea($lat1, $lon1, $lat2, $lon2): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        $c = 2 * asin(sqrt($a));

        return round(self::EARTH_RADIUS * $c, 2);
    }

    public function calculateDurationTime($distanceKm): int
    {
        $now = now();
        $hour = $now->hour;

        // Rush hour is between 7–9 AM and 5–7 PM
        $isRushHour = ($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 19);

        // Average speed: 30 km/h during rush hour, 45 km/h otherwise
        $averageSpeed = $isRushHour ? 30 : 45;

        // Time = distance / speed (in hours)
        $hours = $distanceKm / $averageSpeed;

        // Convert to minutes
        return (int) round($hours * 60);

    }

    /**
     * @throws RideException
     */
    public function calculatePrice($distanceKm): float
    {
        $now = Carbon::now();
        $hour = $now->hour;

        // Define rush hour time range
        $isRushHour = ($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 19);

        $isFlag2 = $hour < 7 || $hour >= 17;

        $rule = null;

        try {
            // Select rule based on conditions
            $rule = PricingRule::query()
                ->when($isFlag2, fn($q) => $q->where('is_flag_2', true))
                ->when(!$isFlag2 && $isRushHour, fn($q) => $q->where('is_rush_hour', true))
                ->when(!$isFlag2 && !$isRushHour, fn($q) => $q->where('name', 'default'))
                ->first();


        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        if (!$rule) {
            throw RideException::pricingRuleNotFound();
        }

        $price = $rule->base_fare + ($rule->price_per_km * $distanceKm);

        return round($price, 2);
    }
}