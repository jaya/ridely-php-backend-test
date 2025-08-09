<?php

namespace Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Integration\IntegrationTestCase;

class RideEstimateSuccessScenarioTest extends IntegrationTestCase
{
    use RefreshDatabase;

    /**
     * Prepare the database before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Reset database for a clean state
//        Artisan::call('migrate:fresh');
    }

    public function test_full_ride_with_estimate_values_success_flow()
    {
        $this->markTestSkipped("To be implemented yet");
    }
}