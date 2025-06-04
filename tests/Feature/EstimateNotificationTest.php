<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Events\EstimateRequested;
use App\Listeners\EstimateNotification;
use App\Services\EstimateService;

class EstimateNotificationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_calculate_on_service()
    {
        $service = Mockery::mock(EstimateService::class);
        $listener = new EstimateNotification($service);

        $event = new EstimateRequested(
            \Mockery::mock(\App\Models\Passenger::class),
            12,
            20
        );

        $service->shouldReceive('calculate')
            ->once()
            ->with(12, 20);

        $listener->handle($event);

        \Mockery::close(); 
        $this->assertTrue(true); 
    }
}
