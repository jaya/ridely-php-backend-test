<?php

namespace App\Observers;

use App\Models\Driver;
use App\Services\DriverCacheService;

class DriverObserver
{
    protected DriverCacheService $driverCacheService;
    public function __construct(DriverCacheService $driverCacheService)
    {
        $this->driverCacheService = $driverCacheService;
    }
    /**
     * Handle the Driver "created" event.
     */
    public function created(Driver $driver): void
    {
        //
        $this->driverCacheService->addDriver($driver);
    }

    /**
     * Handle the Driver "updated" event.
     */
    public function updated(Driver $driver): void
    {
        if ($driver->isDirty('available')) {
            $this->driverCacheService->updateDriver($driver);
        }
    }

    /**
     * Handle the Driver "deleted" event.
     */
    public function deleted(Driver $driver): void
    {
        //
        $this->driverCacheService->deleteDriver($driver);
    }

    /**
     * Handle the Driver "restored" event.
     */
    public function restored(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "force deleted" event.
     */
    public function forceDeleted(Driver $driver): void
    {
        //
    }
}
