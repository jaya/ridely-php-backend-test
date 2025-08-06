<?php

namespace App\Services\Facades;

use App\Http\Criteria\ListCriteria;
use App\Http\Hateos\HateosItemLinks;
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

    public function create($data): array
    {
        $data = $this->driverService->create($data);
        $path = request()->path();
        $newData = $this->addHateosLinksToItems($data, $path);
        return $newData[0];
    }

    public function read($id)
    {
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function list(ListCriteria $criteria):LengthAwarePaginator
    {
        $paginator = $this->driverService->read($criteria);
        if (is_array($paginator->items())) {
            $data = $paginator->items();
            $path = $paginator->path();

            $modifiedItems = $this->addHateosLinksToItems($data, $path);
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

    /**
     * @param $data
     * @param $path
     * @return array
     */
    public function addHateosLinksToItems($data, $path): array
    {
        if ($data instanceof Collection) {
            $items = $data->toArray();
        } else if (!is_array($data)) {
            $items = [$data];
        } else {
            $items = $data;
        }

        $modifiedItems = [];
        foreach ($items as $driver) {

            if (is_object($driver)) {
                $driver = $driver->toArray();
            }

            $self = sprintf("%s/%s", $path, $driver['id']);
            $metadata = [
                '_links' => new HateosItemLinks($self)
            ];
            $modifiedItems[] = array_merge($driver, $metadata);
        }
        return $modifiedItems;

    }
}