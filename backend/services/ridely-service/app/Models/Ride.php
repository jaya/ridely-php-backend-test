<?php

namespace App\Models;

use App\Enums\RideStatusEnum;
use App\Exceptions\RideException;
use App\Http\Criteria\ListCriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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

    public static array $fields = [
        'id',
        'passenger_name',
        'passenger_email',
        'driver_id',
        'status',
        'pick_up',
        'drop_off',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => RideStatusEnum::class
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function estimate()
    {
//        return $this->hasMany(RideEstimate::class);
        return $this->hasOne(RideEstimate::class)->latestOfMany();
    }

    public function request(bool $transaction = false)
    {
        if ($this->status !== null) {
            throw RideException::invalidState('Ride already has a status');
        }

        $this->status = RideStatusEnum::REQUESTED;
        if (!$transaction) {
            $this->save();
        }

    }

    public function accept(Driver $driver, $transaction = false)
    {
        Log::debug(sprintf(
            "Accepting ride %d for driver %s, status: %s",
            $this->id, $driver->name, $this->status->value
        ));

        if ($this->status !== RideStatusEnum::REQUESTED) {
            throw RideException::invalidState('Ride must be in REQUESTED state to be accepted');
        }

        if (!$driver->available) {
            throw RideException::invalidState('Driver is not available');
        }

        $this->driver_id = $driver->id;
        $this->status = RideStatusEnum::ACCEPTED;

        $driver->available = false;

        if (!$transaction) {
            $this->save();
            $driver->save();
        }

    }

    public function finish($transaction = false)
    {
        if ($this->status !== RideStatusEnum::ACCEPTED) {
            throw RideException::invalidState('Ride must be in ACCEPTED state to be finished');
        }

        $this->status = RideStatusEnum::FINISHED;
//        $this->save();

        if ($this->driver) {
            $this->driver->available = true;
//            $this->driver->save();
        }

        if (!$transaction) {
            $this->save();
            if ($this->driver) {
                $this->driver->save();
            }
        }
    }

    public function cancel($transaction = false)
    {
        if (!in_array($this->status, [RideStatusEnum::REQUESTED, RideStatusEnum::ACCEPTED])) {
            throw RideException::invalidState('Ride can only be cancelled in REQUESTED or ACCEPTED state');
        }

        $this->status = RideStatusEnum::CANCELLED;
//        $this->save();

        if ($this->driver) {
            $this->driver->available = true;
//            $this->driver->save();
        }

        if (!$transaction) {
            $this->save();
            if ($this->driver) {
                $this->driver->save();
            }
        }
    }

    public function refuse($transaction = false)
    {
        if ($this->status !== RideStatusEnum::REQUESTED) {
            throw RideException::invalidState('Ride must be in REQUESTED state to be refused');
        }

        $this->status = RideStatusEnum::REFUSED;
        if (!$transaction) {
            $this->save();
        }
    }

    public function find(int $id, $loaded = false): Ride
    {
        try {
            if (!$loaded) {
                return self::findOrFail($id);
            }
            return self::with(['driver', 'estimate'])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw RideException::notFound();
        }
    }

    public function withoutDriver(ListCriteria $criteria): LengthAwarePaginator
    {
        $query = self::query();
        if ($criteria->fields) {
            $query->select($criteria->fields);
        }
        $query->orderBy($criteria->orderBy, $criteria->sortBy);
        $query->where('status', RideStatusEnum::REQUESTED)
            ->whereNull('driver_id');

        $perPage = $criteria->limit ?? ListCriteria::LIMIT;
        $currentPage = $criteria->page ?? ListCriteria::PAGE;

        Log::debug($query->toSql());
        Log::debug("pagination params: \$perPage: $perPage, \$currentPage: $currentPage");
        return $query->paginate($perPage, ['*'], 'page', $currentPage);
    }
}
