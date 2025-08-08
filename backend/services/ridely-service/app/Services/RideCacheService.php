<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\Driver;

class RideCacheService
{
    private string $cacheKey = 'available_drivers';

    /**
     * Get the next available driver from cache or DB as fallback.
     */
    public function getNextAvailableDriver(): ?Driver
    {
        Log::info("Fetching next available driver from cache.");
        // Try getting the first driver from cache
        $driverId = Redis::zRange($this->cacheKey, 0, 0)[0] ?? null;

        // If cache is empty, reload from database
        if (!$driverId) {
            $this->refreshCacheFromDatabase();
            $driverId = Redis::zRange($this->cacheKey, 0, 0)[0] ?? null;
        }

        return $driverId ? Driver::find($driverId) : null;
    }

    /**
     * Remove a driver from the cache (when they accept a ride).
     */
    public function removeDriverFromCache(int $driverId): void
    {
        Log::info("Removing driver ID $driverId from cache.");
        Redis::zRem($this->cacheKey, $driverId);
    }

    /**
     * Add or update a driver in the cache.
     */
    public function addDriverToCache(Driver $driver): void
    {
        Redis::zAdd($this->cacheKey, strtotime($driver->activation_date), $driver->id);
    }

    /**
     * Reload the cache from the database.
     */
    public function refreshCacheFromDatabase(): void
    {
        Log::info("Refreshing drivers cache from database.");

        $drivers = Driver::where('available', true)
            ->orderBy('activation_date', 'asc')
            ->get(['id', 'activation_date']);

        foreach ($drivers as $driver) {
            Redis::zAdd($this->cacheKey, strtotime($driver->activation_date), $driver->id);
        }
    }
}
