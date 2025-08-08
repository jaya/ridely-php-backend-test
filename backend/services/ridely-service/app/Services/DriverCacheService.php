<?php

namespace App\Services;

use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class DriverCacheService
{
    private $driversExpirationTime = 60;

    private $driversIdExpirationTime = 600;

    private string $driversCacheKey = 'drivers:sorted';

    // Note: for futere use, we can also store driver IDs in a sorted set
    private string $driversIdsCacheKey = 'drivers:ids';


    // TODO: Considerar um job para automatizar essas rotinas
    public function allDrivers(): void
    {
        Log::info('Fetching all drivers from the database.');
        // Retrieve all drivers sorted by activation_date
        $drivers = Driver::orderBy('activation_date', 'asc')->get();

        // Clear the old cache
//        Redis::del($this->driversIdsCacheKey);
        Redis::del($this->driversCacheKey);

        Log::info('Storing all drivers in cache.', ['count' => $drivers->count()]);
        foreach ($drivers as $driver) {
            $this->addDriver($driver);
        }

//        Redis::expire($this->driversIdsCacheKey, $this->driversIdExpirationTime);
        Redis::expire($this->driversCacheKey, $this->driversExpirationTime);
    }

    public function updateDriver(Driver $driver): void
    {
        $this->deleteDriver($driver);
        // Update only the specific driver
        $this->addDriver($driver);
    }

    public function refreshCacheFromDatabase(): void
    {
        Log::info("Refreshing all drivers cache from database.");

        $this->allDrivers();

    }

    public function addDriver(Driver $driver)
    {
        $driverId = $driver->id;
        $activationDateTimestamp = $driver->activation_date ? strtotime($driver->activation_date) : time();
        // Store the driver ID in a sorted set with activation_date as the score
//        Redis::zAdd($this->driversIdsCacheKey, $activationDateTimestamp, $driverId);

        Redis::hMSet("$this->driversCacheKey:$driverId", $driver->toArray());

    }

    public function deleteDriver(Driver $driver)
    {
//        Redis::zRem($this->driversIdsCacheKey, $driver->id);
        Redis::del("$this->driversCacheKey:$driver->id");
    }

    public function getDriver(string $driverId): ?Driver
    {
        $data = Redis::hGetAll("$this->driversCacheKey:$driverId");
        Log::info('Fetching driver from cache...', ['driver_id' => $driverId, 'data' => $data]);
        if (empty($data)) {
            return null;
        }
        $driver = new Driver($data);
        $driver->id = $driverId; // Ensure the ID is set correctly
        return $driver;
    }
}