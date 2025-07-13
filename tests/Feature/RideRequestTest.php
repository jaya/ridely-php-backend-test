<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Ride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RideRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_allocates_the_nearest_driver()
    {
        // Cria dois motoristas em distâncias diferentes
        // Create two drivers at different distances
        $nearDriver = Driver::factory()->create([
            'latitude' => -10.900000,
            'longitude' => -37.070000,
            'name' => 'Driver 1 Near'
        ]);

        $farDriver = Driver::factory()->create([
            'latitude' => -10.500000,
            'longitude' => -37.000000,
            'name' => 'Driver 2 Far'
        ]);

        // Simula requisição de corrida
        $response = $this->postJson('/api/rides/request-driver', [
            'passenger' => [
                'name' => 'João Passageiro',
                'email' => 'joao@email.com'
            ],
            'pick_up' => [
                'latitude' => -10.901000,
                'longitude' => -37.071000
            ],
            'drop_off' => [
                'latitude' => -10.905000,
                'longitude' => -37.073000
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('driver.name', 'Driver 1 Near');
        $response->assertJsonPath('status', 'REQUESTED');
        $response->assertJsonPath('pick_up', '-10.901,-37.071');
        $response->assertJsonPath('drop_off', '-10.905,-37.073');

        $this->assertDatabaseHas('rides', [
            'passenger_name' => 'João Passageiro',
            'driver_id' => $nearDriver->id
        ]);
    }

    public function test_it_returns_error_if_no_drivers_available()
    {
        $response = $this->postJson('/api/rides/request-driver', [
            'passenger' => [
                'name' => 'Sem Motorista',
                'email' => 'nobody@nowhere.com'
            ],
            'pick_up' => [
                'latitude' => -10.91,
                'longitude' => -37.08
            ],
            'drop_off' => [
                'latitude' => -10.92,
                'longitude' => -37.09
            ]
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'error' => 'We do not have drivers available'
        ]);
    }
}
