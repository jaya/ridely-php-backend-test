<?php

namespace App\Models;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\DriverException;
use App\Exceptions\RepositoryException;
use App\Http\Criteria\ListCriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'car_license_plate',
        'car_model',
        'car_color',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public static array $fields = [
        'id',
        'name',
        'activation_date',
        'car_license_plate',
        'car_model',
        'car_color',
        'available',
        'created_at',
        'updated_at'
    ];

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function becomeAvailable()
    {
        $this->available = true;
        $this->save();
    }

    public function becomeBusy()
    {
        $this->available = false;
        $this->save();
    }

    public function getOpenRides()
    {
        return $this->rides()
            ->where('status', Ride::STATUS_REQUESTED)
            ->get();
    }

    // TODO modificar para não ficar estático
    public static function allDrivers(ListCriteria $criteria): LengthAwarePaginator
    {
        $query = self::query();
        if ($criteria->fields) {
            $query->select($criteria->fields);
        }
        $query->orderBy($criteria->orderBy, $criteria->sortBy);
        $perPage = $criteria->limit ?? ListCriteria::LIMIT;
        $currentPage = $criteria->page ?? ListCriteria::PAGE;
        Log::debug($query->toSql());
        Log::debug("pagination params: \$perPage: $perPage, \$currentPage: $currentPage");
        return $query->paginate($perPage, ['*'], 'page', $currentPage);
    }
}
