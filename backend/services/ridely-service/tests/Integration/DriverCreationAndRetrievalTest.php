<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\Artisan;

class DriverCreationAndRetrievalTest extends IntegrationTestCase
{

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
     * Full flow: authenticate, create driver, retrieve driver
     */
    public function test_full_driver_flow()
    {
        $authResponse = $this->authenticateAndGetAccessToken();

        $this->assertAuthResponse($authResponse);

        $token = $authResponse->json('access_token');

        // Create a driver using the access token
        $driverResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/drivers', [
                'name' => 'Carlos',
                'car' => [
                    'license_plate' => 'ABC1234',
                    'model' => 'Fiat Uno',
                    'color' => 'Vermelho'
                ],
                'available' => true
            ]);

        $driverResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'label' => 'success',
                'code' => 0,
                'message' => 'Success',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'car' => [
                        'license_plate',
                        'model',
                        'color'
                    ],
                    'available',
                    '_links' => [
                        'self',
                        'update',
                        'replace',
                        'delete'
                    ]
                ]
            ]);

        // Save driver ID for next step
        $driverId = $driverResponse->json('data.id');

        // Retrieve the driver using the ID
        $getDriverResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/drivers/{$driverId}");

        $getDriverResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $driverId,
                    'name' => 'Carlos',
                    'car' => [
                        'license_plate' => 'ABC1234',
                        'model' => 'Fiat Uno',
                        'color' => 'Vermelho'
                    ],
                    'available' => true
                ]
            ]);
    }
}
