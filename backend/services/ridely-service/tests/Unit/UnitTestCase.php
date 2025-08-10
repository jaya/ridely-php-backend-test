<?php

namespace Tests\Unit;

use App\Models\Driver;
use App\Models\PricingRule;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\DriverCacheService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Services\Interfaces\LocationServiceInterface;
use App\Services\RideCacheService;
use App\Services\V1\DriverService;
use App\Services\V1\LocationService;
use App\Services\V1\RideEstimateService;
use App\Services\V1\RideService;
use App\Validators\DriverValidator;
use App\Validators\LocationValidator;
use App\Validators\RideEstimateValidator;
use App\Validators\RideValidator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Mockery;
use Tests\TestCase;

class UnitTestCase extends TestCase
{

    /**
     * @var Mockery\MockInterface&PricingRule
     */
    protected Mockery\MockInterface&PricingRule $pricingRuleModelMock;
    /**
     * @var Mockery\MockInterface&Ride
     */
    protected Mockery\MockInterface&Ride $rideModelMock;

    /**
     * @var Mockery\MockInterface&Driver
     */
    protected Mockery\MockInterface&Driver $driverModelMock;

    /**
     * @var RideEstimate&Mockery\MockInterface&RideEstimate
     */
    protected Mockery\MockInterface&RideEstimate $rideEstimateModelMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockRideCacheService();
        $this->mockDriverCacheService();
        $this->mockDatabaseConnection();
    }

    public function mockDatabaseConnection(): \Mockery\MockInterface
    {
        // Cria um mock de conexão
        $connectionMock = Mockery::mock(ConnectionInterface::class);

        // Configura respostas para queries comuns
        $connectionMock->shouldReceive('select')->andReturn([]);
        $connectionMock->shouldReceive('insert')->andReturn(true);
        $connectionMock->shouldReceive('update')->andReturn(1);
        $connectionMock->shouldReceive('delete')->andReturn(1);
        $connectionMock->shouldReceive('statement')->andReturn(true);
        $connectionMock->shouldReceive('affectingStatement')->andReturn(1);

        $dbMock = \Mockery::mock(\Illuminate\Database\DatabaseManager::class);

        $dbMock->shouldReceive('getDefaultConnection')
            ->andReturn('mysql');

        $dbMock->shouldReceive('connection')
            ->andReturn($connectionMock);

        // Mock alias para a classe Ride
//        Mockery::mock('alias:App\Models\Ride');
//        Mockery::mock('alias:App\Models\Driver');
//        Mockery::mock('alias:App\Models\RideEstimate');


        return $connectionMock;
    }

    public function getDriverModelMock(): \Mockery\MockInterface&Driver
    {
        //return Mockery::mock(Driver::class);
        return $this->createModelMockWithData(Driver::class);
    }

    public function getRideModelMock(): \Mockery\MockInterface&Ride
    {
        return $this->createModelMockWithData(Ride::class);
    }

    public function getRideEstimateModelMock(): \Mockery\MockInterface&RideEstimate
    {
        return $this->createModelMockWithData(RideEstimate::class);
    }

    public function getPricingRuleModelMock(): \Mockery\MockInterface&PricingRule
    {
        return $this->createModelMockWithData(PricingRule::class);
    }




    public function createModelMockWithData($class, $data = null): mixed
    {
        $builderMock = Mockery::mock(Builder::class);

        $mock = Mockery::mock($class)->makePartial();
        $mock->shouldReceive('load')->andReturnSelf();
        $mock->shouldReceive('isIgnoringTimestamps')->andReturn(false);
        $mock->shouldReceive('save')->andReturnSelf();
        $mock->shouldReceive('with')->andReturnSelf();
        $mock->shouldReceive('query')->andReturn($builderMock);

        if (isset($data)) {
            foreach ($data as $key => $value) {
                $mock->$key = $value;
            }
        }


        return $mock;
    }

    /**
     * @param $rideCacheServiceMock
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function mockRideService($rideCacheServiceMock): void
    {
        $this->rideModelMock = $this->getRideModelMock();
        $rideService = new RideService(
            $this->rideModelMock,
            $this->app->get(RideValidator::class),
            $this->app->get(LocationServiceInterface::class),
            $rideCacheServiceMock);
        $this->app->instance(RideService::class, $rideService);
    }

    /**
     * @param $locationServiceUrl
     * @return LocationService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function mockLocationService($locationServiceUrl): LocationService
    {
        $this->pricingRuleModelMock = $this->getPricingRuleModelMock();
        $locationService = new LocationService(
            $this->pricingRuleModelMock,
            $this->app->get(LocationValidator::class),
            $locationServiceUrl
        );
        $this->app->instance(LocationServiceInterface::class, $locationService);
        return $locationService;
    }

    /**
     * @param LocationService $locationService
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function mockRideEstimateService(LocationService $locationService): void
    {
        $this->rideEstimateModelMock = $this->getRideEstimateModelMock();
        $rideEstimateService = new RideEstimateService(
            $this->rideEstimateModelMock,
            $this->app->get(RideEstimateValidator::class),
            $locationService
        );

        $this->app->instance(RideEstimateService::class, $rideEstimateService);
    }

    /**
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function mockDriverService(): void
    {
        $this->driverModelMock = $this->getDriverModelMock();
        $driverService = new DriverService($this->driverModelMock, $this->app->get(DriverValidator::class), $this->app->get(DriverCacheService::class));
        $this->app->instance(DriverServiceInterface::class, $driverService);
    }

}
