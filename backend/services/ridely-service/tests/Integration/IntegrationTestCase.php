<?php

namespace Tests\Integration;

use App\Services\DriverCacheService;
use App\Services\RideCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Redis;
use Tests\TestCase;

class IntegrationTestCase extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        $this->mockRideCacheService();
        $this->mockDriverCacheService();
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