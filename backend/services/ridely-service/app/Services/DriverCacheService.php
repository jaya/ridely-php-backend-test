<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Exception;

class DriverCacheService
{
    private $driversExpirationTime = 60;

    private $driversIdExpirationTime = 600;
    /**
     * @var array|\class-string[]
     */
    protected static array $context = [];

    private string $driversCacheKey = 'drivers:sorted';

    // Note: for futere use, we can also store driver IDs in a sorted set
    private string $driversIdsCacheKey = 'drivers:ids';


    // TODO: Considerar um job para automatizar essas rotinas
    public function allDrivers(): void
    {
        Log::info('Fetching all drivers from the database.', $this->context());
        // Retrieve all drivers sorted by activation_date
        $drivers = Driver::orderBy('activation_date', 'asc')->get();

        try {
            // Clear the old cache
//        Redis::del($this->driversIdsCacheKey);
            Redis::del($this->driversCacheKey);
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

        Log::info('Storing all drivers in cache.', array_merge($this->context(), ['count' => $drivers->count()]));
        foreach ($drivers as $driver) {
            $this->addDriver($driver);
        }

        try {
            // Clear the old cache
//        Redis::expire($this->driversIdsCacheKey, $this->driversIdExpirationTime);
            Redis::expire($this->driversCacheKey, $this->driversExpirationTime);
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }


    }

    public function updateDriver(Driver $driver): void
    {
        $this->deleteDriver($driver);
        // Update only the specific driver
        $this->addDriver($driver);
    }

    public function refreshCacheFromDatabase(): void
    {
        Log::info("Refreshing all drivers cache from database.", $this->context());

        $this->allDrivers();

    }

    public function addDriver(Driver $driver)
    {
        $driverId = $driver->id;
        $activationDateTimestamp = $driver->activation_date ? strtotime($driver->activation_date) : time();

        try {
            // Store the driver ID in a sorted set with activation_date as the score
//        Redis::zAdd($this->driversIdsCacheKey, $activationDateTimestamp, $driverId);
            Redis::hMSet("$this->driversCacheKey:$driverId", $driver->toArray());
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

    }

    public function deleteDriver(Driver $driver)
    {
        try {
            //        Redis::zRem($this->driversIdsCacheKey, $driver->id);
            Redis::del("$this->driversCacheKey:$driver->id");
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }
    }

    public function getDriver(string $driverId): ?Driver
    {
        $driver = null;
        $data = null;

        try {
            $data = Redis::hGetAll("$this->driversCacheKey:$driverId");
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

        Log::info('Fetching driver from cache...', array_merge($this->context(), ['driver_id' => $driverId, 'data' => $data]));
        if (empty($data)) {
            return null;
        }

        try {
            $driver = new Driver($data);
            $driver->id = $driverId; // Ensure the ID is set correctly
        } catch (\Throwable $e) {
            $ex = ServiceException::cacheOperationFailure($e->getMessage());
            Log::error($ex->getMessage());
            throw $ex;
        }

        return $driver;
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