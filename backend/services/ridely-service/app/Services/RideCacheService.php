<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\Driver;

class RideCacheService
{
    private string $availableDriversCacheKey = 'available_drivers';
    private DriverCacheService $driverCacheService;

    public function __construct(DriverCacheService $driverCacheService)
    {
        $this->driverCacheService = $driverCacheService;
    }
    /**
     * Get the next available driver from cache or DB as fallback.
     */
    public function getNextAvailableDriver(): ?Driver
    {
        Log::info("Fetching next available driver from cache.");
        // Try getting the first driver from cache
        $driverId = $this->getDriverId();

        // If cache is empty, reload from database
        if (!$driverId) {
            Log::debug("No available drivers found in cache, refreshing from database.");
            $this->refreshCacheFromDatabase();
            return null;
        }

        Log::debug("Found available driver ID $driverId in cache.");
        $driver = $this->driverCacheService->getDriver($driverId);
        if (!$driver) {
            $this->driverCacheService->refreshCacheFromDatabase();
            return null;
        }

        return $driver;
    }

    /**
     * Remove a driver from the cache (when they accept a ride).
     */
    public function removeDriverFromCache(int $driverId): void
    {
        Log::info("Removing driver ID $driverId from cache.");
        Redis::zRem($this->availableDriversCacheKey, $driverId);
    }

    /**
     * Add or update a driver in the cache.
     */
    public function addDriverToCache(Driver $driver): void
    {
        Redis::zAdd($this->availableDriversCacheKey, strtotime($driver->activation_date), $driver->id);
    }

    /**
     * Reload the cache from the database.
     */
    public function refreshCacheFromDatabase(): void
    {
        Log::info("Refreshing available drivers cache from database.");

        $this->availableDrivers();

    }

    /**
     * @return void
     */
    public function availableDrivers(): void
    {
        $drivers = Driver::where('available', true)
            ->orderBy('activation_date', 'asc')
            ->get(['id', 'activation_date']);

        foreach ($drivers as $driver) {
            Redis::zAdd($this->availableDriversCacheKey, strtotime($driver->activation_date), $driver->id);
        }
    }

    /**
     * @return mixed|null
     */
    public function getDriverId(): mixed
    {
        $driverId = Redis::zRange($this->availableDriversCacheKey, 0, 0)[0] ?? null;
        return $driverId;
    }
}
