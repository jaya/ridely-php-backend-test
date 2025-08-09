<?php

namespace Tests\Integration;

use App\Services\DriverCacheService;
use App\Services\RideCacheService;
use Redis;
use Tests\TestCase;

class IntegrationTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        // Mocks to prevent the usage of cache
        $rideCacheServiceMock = \Mockery::mock(RideCacheService::class)->makePartial();
        $rideCacheServiceMock
            ->shouldReceive('getDriverId')
            ->andReturn(null);

        $this->app->instance(RideCacheService::class, $rideCacheServiceMock);

        $driverCacheServiceMock = \Mockery::mock(DriverCacheService::class);
        $driverCacheServiceMock
            ->shouldReceive('getDriver')
            ->andReturn(null);
        $driverCacheServiceMock->shouldReceive('addDriver');
        $driverCacheServiceMock->shouldReceive('updateDriver');

        $this->app->instance(DriverCacheService::class, $driverCacheServiceMock);

    }


    /**
     * @return \Illuminate\Testing\TestResponse
     */
    public function authenticateAndGetAccessToken(): \Illuminate\Testing\TestResponse
    {
        // Authenticate and get access token
        $authResponse = $this->postJson('/api/auth/login', [
            'username' => 'integration_test_user1',
            'password' => 'senha123',
        ]);
        return $authResponse;
    }

    /**
     * @param \Illuminate\Testing\TestResponse $authResponse
     * @return void
     */
    public function assertAuthResponse(\Illuminate\Testing\TestResponse $authResponse): void
    {
        $authResponse->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }
}