<?php

namespace App\Listeners;

use App\Events\EstimateRequested;
use App\Services\EstimateService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EstimateNotification implements ShouldQueue
{
    /**
     * Create a new job instance.
     */
    public function __construct(protected EstimateService $estimateService)
    {}

    /**
     * Handle the event.
     */
    public function handle(EstimateRequested $event): void
    {
        $this->estimateService->calculate(
            $event->distanceKm, 
            $event->timeInMinutes
        );
    }
}