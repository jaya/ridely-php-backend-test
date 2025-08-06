<?php

namespace App\Models;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RideStatusEnum;
use App\Exceptions\RideException;
use App\Exceptions\ServiceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'passenger_name',
        'passenger_email',
        'driver_id',
        'status',
        'pick_up',
        'drop_off',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];


    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function request()
    {
        if ($this->status !== null) {
            throw RideException::invalidState('Ride already has a status');
        }

        $this->status = RideStatusEnum::REQUESTED;
        $this->save();
    }

    public function accept(Driver $driver)
    {
        if ($this->status !== RideStatusEnum::REQUESTED) {
            throw RideException::invalidState('Ride must be in REQUESTED state to be accepted');
        }

        if (!$driver->available) {
            throw RideException::invalidState('Driver is not available');
        }

        $this->driver_id = $driver->id;
        $this->status = RideStatusEnum::ACCEPTED;
        $this->save();

        $driver->available = false;
        $driver->save();
    }

    public function finish()
    {
        if ($this->status !== RideStatusEnum::ACCEPTED) {
            throw RideException::invalidState('Ride must be in ACCEPTED state to be finished');
        }

        $this->status = RideStatusEnum::FINISHED;
        $this->save();

        if ($this->driver) {
            $this->driver->available = true;
            $this->driver->save();
        }
    }

    public function cancel()
    {
        if (!in_array($this->status, [RideStatusEnum::REQUESTED, RideStatusEnum::ACCEPTED])) {
            throw RideException::invalidState('Ride can only be cancelled in REQUESTED or ACCEPTED state');
        }

        $this->status = RideStatusEnum::CANCELLED;
        $this->save();

        if ($this->driver) {
            $this->driver->available = true;
            $this->driver->save();
        }
    }

    public function refuse()
    {
        if ($this->status !== RideStatusEnum::REQUESTED) {
            throw RideException::invalidState('Ride must be in REQUESTED state to be refused');
        }

        $this->status = RideStatusEnum::REFUSED;
        $this->save();
    }

    // TODO revisar se é o driver ou ride, revisar nome do metodo
    public function getRideWithDriver(int $id)
    {
        try {
            return self::with('driver')->findOrFail($id);
        } catch (\Exception $e) {
            //throw ServiceException::notFound(ErrorMessagesEnum::RIDE_NOT_FOUND, ["id" => $id], $e);
            throw RideException::notFound(["id" => $id]);
        }
    }
}
