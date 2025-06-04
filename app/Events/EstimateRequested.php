<?php

namespace App\Events;

use App\Models\Passenger;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstimateRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Passenger $passenger;
    public int $distanceKm;
    public int $timeInMinutes;

    /**
     * Create a new event instance.
     */
    public function __construct(Passenger $passenger, int $distanceKm, int $timeInMinutes)
    {
        $this->passenger = $passenger;
        $this->distanceKm = $distanceKm;
        $this->timeInMinutes = $timeInMinutes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('estimate-channel'),
        ];
    }
}
