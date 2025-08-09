<?php

namespace App\Services\Facades;

use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Http\Hateos\HateosHelper;
use App\Http\Hateos\HateosItemLinks;
use App\Models\Driver;
use App\Services\Interfaces\DriverServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DriverManagerFacade
{
    protected DriverServiceInterface $driverService;
    public function __construct(
        DriverServiceInterface $driverService
    )
    {
        $this->driverService = $driverService;
    }

    public function create(CreateDriverCriteria $criteria): Driver
    {
        return $this->driverService->create($criteria);
    }

    public function read($id)
    {
    }

    public function update()
    {

    }

    public function delete($id)
    {
        $this->driverService->delete($id);
        return true;
    }

    public function softDelete($id)
    {

    }

    public function list(ListCriteria $criteria):LengthAwarePaginator
    {
        $paginator = $this->driverService->read($criteria);
        if (is_array($paginator->items())) {
            $data = $paginator->items();
            $path = $paginator->path();

            $modifiedItems = HateosHelper::addHateosLinksToItems($data, $path);
            return new LengthAwarePaginator(
                $modifiedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }

        return $paginator;

    }

    public function find(string $id)
    {
        return $this->driverService->find($id);
    }

    public function getOpenRides($id, ListCriteria $criteria)
    {
        $paginator = $this->driverService->getOpenRides($id, $criteria);
        if (is_array($paginator->items())) {
            $data = $paginator->items();
            $path = str_replace("/{$id}/get-rides", "", $paginator->path());
            $path = str_replace("drivers", "rides", $path);

            $modifiedItems = HateosHelper::addHateosLinksToItems($data, $path);
            return new LengthAwarePaginator(
                $modifiedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }

        return $paginator;
    }


}