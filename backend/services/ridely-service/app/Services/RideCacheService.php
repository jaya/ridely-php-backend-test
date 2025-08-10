<?php

namespace App\Services;

use App\Enums\RedisStreamsEnum;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\Driver;

class RideCacheService
{
    private string $availableDriversCacheKey = 'available_drivers';
    private DriverCacheService $driverCacheService;
    /**
     * @var array|\class-string[]
     */
    protected static array $context = [];

    public function __construct(DriverCacheService $driverCacheService)
    {
        $this->driverCacheService = $driverCacheService;

    }

    /**
     * Get the next available driver from cache or DB as fallback.
     */
    public function getNextAvailableDriver(): ?Driver
    {
        Log::info("Fetching next available driver from cache.", $this->context());
        // Try getting the first driver from cache
        $driverId = $this->getDriverId();

        // If cache is empty, reload from database
        if (!$driverId) {
            Log::debug("No available drivers found in cache, refreshing from database.", $this->context());
            $this->refreshCacheFromDatabase();
            return null;
        }

        Log::debug("Found available driver ID $driverId in cache.", $this->context());
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
        Log::info("Removing driver ID $driverId from cache.", $this->context());
        try {
            Redis::zRem($this->availableDriversCacheKey, $driverId);
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

    }

    /**
     * Add or update a driver in the cache.
     */
    public function addDriverToCache(Driver $driver): void
    {
        try {

            Redis::zAdd($this->availableDriversCacheKey, strtotime($driver->activation_date), $driver->id);
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }
    }

    /**
     * Reload the cache from the database.
     */
    public function refreshCacheFromDatabase(): void
    {
        Log::info("Refreshing available drivers cache from database.", $this->context());

        $this->availableDrivers();

    }

    /**
     * @return void
     */
    public function availableDrivers(): void
    {
        Log::info("Fetching available drivers from the DB");
        $drivers = Driver::where('available', true)
            ->orderBy('activation_date', 'asc')
            ->get(['id', 'activation_date']);

        try {
            foreach ($drivers as $driver) {
                Redis::zAdd($this->availableDriversCacheKey, strtotime($driver->activation_date), $driver->id);
            }
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

    }

    /**
     * @return mixed|null
     */
    public function getDriverId(): mixed
    {
        try {
            $driverId = Redis::zRange($this->availableDriversCacheKey, 0, 0)[0] ?? null;
            return $driverId;
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

    }

    /**
     * @param $ride
     * @return void
     */
    public function addRideToStream($ride): void
    {
        try {
            Redis::xadd(RedisStreamsEnum::RIDE_ESTIMATES_STREAM->value, '*', [
                'ride_id' => $ride->id,
                'estimate_id' => $ride->estimate->id,
                'pick_up' => $ride->pick_up,
                'drop_off' => $ride->drop_off,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

    }

    private function context()
    {
        if (!self::$context) {
            self::$context = [
                "class" => self::class
            ];
        }

        return self::$context;

    }
}
