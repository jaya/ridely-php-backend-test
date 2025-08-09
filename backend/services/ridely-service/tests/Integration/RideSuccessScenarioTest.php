<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RideSuccessScenarioTest extends IntegrationTestCase
{
    use RefreshDatabase;

    /**
     * Prepare the database before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Reset database for a clean state
        Artisan::call('migrate:fresh');
    }

    /**
     * Test full ride lifecycle with success scenario
     */
    public function test_full_ride_success_flow()
    {
        $authResponse = $this->authenticateAndGetAccessToken();

        $this->assertAuthResponse($authResponse);

        $token = $authResponse->json('access_token');

        // Step 1: Create a driver
        $driverData = [
            'name' => 'Helen Fritsch',
            'car' => [
                'license_plate' => 'GBJ-2522',
                'model' => 'laborum',
                'color' => 'blue'
            ]
        ];

        $createDriverResponse =$this->withHeader('Authorization', 'Bearer ' . $token)->
        postJson('/api/v1/drivers', $driverData);

        $createDriverResponse->assertStatus(201);
        $driverId = $createDriverResponse->json('data.id');
        $availability = $createDriverResponse->json('data.available');

        Log::debug("driver: $driverId availability: $availability");

        // Step 2: Request a ride
        $rideRequestData = [
            'passenger' => [
                'name' => 'Maria',
                'email' => 'maria@email.com'
            ],
            'pick_up' => 'Avenida Beira Mar, 25',
            'drop_off' => 'Avenida Euclides Figueiredo, 65'
        ];

        $requestRideResponse = $this->postJson('/api/v1/rides/request-driver', $rideRequestData);
        $requestRideResponse->assertStatus(201);
        $rideId = $requestRideResponse->json('data.id');

        // Step 3: Accept the ride
        // Note: in a real scenario, you may need to run the queue worker
        // For example: Artisan::call('queue:process-ride-estimates');
        $acceptRideResponse = $this->postJson("/api/v1/rides/{$rideId}/accept-ride");
        $acceptRideResponse->assertStatus(200);
        $this->assertEquals('ACCEPTED', $acceptRideResponse->json('data.status'));

        // Step 4: Check ride details until estimate is ready
        // In real scenario, this might require waiting or faking the job processing
        $rideDetailsResponse = $this->getJson("/api/v1/rides/{$rideId}");
        $rideDetailsResponse->assertStatus(200);
        // Here you can assert specific values once the estimate is processed
        // Example:
        // $this->assertEquals('READY', $rideDetailsResponse->json('data.estimate.status'));

        // Step 5: Finish the ride
        $finishRideResponse = $this->postJson("/api/v1/rides/{$rideId}/finish-ride");
        $finishRideResponse->assertStatus(200);
        $this->assertEquals('FINISHED', $finishRideResponse->json('data.status'));

        // Step 6: Confirm final status
        $finalRideDetails = $this->getJson("/api/v1/rides/{$rideId}");
        $finalRideDetails->assertStatus(200);
        $this->assertEquals('FINISHED', $finalRideDetails->json('data.status'));
    }
}
