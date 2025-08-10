<?php

namespace Tests;

use App\Services\DriverCacheService;
use App\Services\RideCacheService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var \Mockery\LegacyMockInterface|(\Mockery\MockInterface&RideCacheService)
     */
    protected \Mockery\MockInterface|RideCacheService|\Mockery\LegacyMockInterface $rideCacheServiceMock;
    /**
     * @var DriverCacheService|\Mockery\LegacyMockInterface|\Mockery\MockInterface|(\Mockery\MockInterface&DriverCacheService)
     */
    protected DriverCacheService|\Mockery\LegacyMockInterface|\Mockery\MockInterface $driverCacheServiceMock;

    public static function setUpBeforeClass(): void
    {
        // Fallback for IDE when it is not configured properly
        $dbConnection = env('DB_CONNECTION');
        if (!$dbConnection) {
            $path = str_replace("tests", "", getcwd());
            exit("Env not defined. phpunit.xml not being used as the configuration file. Set the path {$path}/phpunit.xml");
        }

    }



    public function mockRideCacheService():void
    {
        $this->rideCacheServiceMock =$this->getRideCacheService();

        $this->rideCacheServiceMock->shouldReceive('getDriverId')->andReturn(null);
        $this->rideCacheServiceMock->shouldReceive('removeDriverFromCache');
        $this->rideCacheServiceMock->shouldReceive('addRideToStream');
        $this->rideCacheServiceMock->shouldReceive('availableDrivers');
        $this->rideCacheServiceMock->shouldReceive('getDriverId');


        $this->app->instance(RideCacheService::class, $this->rideCacheServiceMock);

    }

    public function mockDriverCacheService(): void
    {
        $this->driverCacheServiceMock = $this->getDriverCacheService();
        $this->driverCacheServiceMock->shouldReceive('getDriver')->andReturn(null);
        $this->driverCacheServiceMock->shouldReceive('addDriver');
        $this->driverCacheServiceMock->shouldReceive('updateDriver');
        $this->driverCacheServiceMock->shouldReceive('deleteDriver');

        $this->app->instance(DriverCacheService::class, $this->driverCacheServiceMock);
    }

    /**
     * @return \Mockery\LegacyMockInterface|(\Mockery\MockInterface&DriverCacheService)
     */
    public function getDriverCacheService(): \Mockery\LegacyMockInterface|\Mockery\MockInterface|DriverCacheService
    {
        return \Mockery::mock(DriverCacheService::class)->makePartial();
    }

    /**
     * @return \Mockery\LegacyMockInterface|(\Mockery\MockInterface&RideCacheService)
     */
    public function getRideCacheService()
    {
        return \Mockery::mock(RideCacheService::class)->makePartial();
    }
}
