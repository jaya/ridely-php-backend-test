<?php

namespace Tests\Feature;

use App\Events\EstimateRequested;
use App\Models\Passenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;


class EstimateControllerTest extends TestCase
{
    public function test_estimate_dispatches_event(): void
    {
        Event::fake();

        $passenger = Passenger::factory()->make();

        $this->actingAs($passenger)
            ->postJson('/api/estimates/calculate', [
                'distance_km' => 10,
                'time_in_minutes' => 15
            ])
            ->assertOk();

        Event::assertDispatched(EstimateRequested::class, function ($event) use ($passenger) {
            return $event->passenger->is($passenger) &&
                   $event->distanceKm === 10 &&
                   $event->timeInMinutes === 15;
        });
    }
}
