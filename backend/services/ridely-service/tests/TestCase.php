<?php

namespace Tests;

use App\Services\DriverCacheService;
use App\Services\RideCacheService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static function setUpBeforeClass(): void
    {
        // Fallback for IDE when it is not configured properly
        $dbConnection = env('DB_CONNECTION');
        if (!$dbConnection) {
            $path = str_replace("tests", "", getcwd());
            exit("Env not defined. phpunit.xml not being used as the configuration file. Set the path {$path}/phpunit.xml");
        }

    }

    public function mockRideCacheService(): \Mockery\LegacyMockInterface
    {

        $rideCacheServiceMock = \Mockery::mock(RideCacheService::class)->makePartial();
        $rideCacheServiceMock
            ->shouldReceive('getDriverId')
            ->andReturn(null);
        $rideCacheServiceMock->shouldReceive('addRideToStream');

        $this->app->instance(RideCacheService::class, $rideCacheServiceMock);

        return $rideCacheServiceMock;
    }

    /**
     * @return DriverCacheService|(DriverCacheService&\Mockery\MockInterface&object&\Mockery\LegacyMockInterface)|(\Mockery\MockInterface&object&\Mockery\LegacyMockInterface)
     */
    public function mockDriverCacheService()
    {
        $driverCacheServiceMock = \Mockery::mock(DriverCacheService::class)->makePartial();
        $driverCacheServiceMock
            ->shouldReceive('getDriver')
            ->andReturn(null);
        $driverCacheServiceMock->shouldReceive('addDriver');
        $driverCacheServiceMock->shouldReceive('updateDriver');
        $driverCacheServiceMock->shouldReceive('deleteDriver');

        $this->app->instance(DriverCacheService::class, $driverCacheServiceMock);

        return $driverCacheServiceMock;
    }
}
