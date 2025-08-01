<?php

namespace App\Services\Facades;

use App\Http\Criteria\Criteria;
use App\Http\Hateos\HateosItemLinks;
use App\Services\Interfaces\Driver\CreateDriverServiceInterface;
use App\Services\Interfaces\Driver\ReadDriverServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DriverManagerFacade
{
    protected CreateDriverServiceInterface $createService;
    protected ReadDriverServiceInterface $readService;
    public function __construct(
        CreateDriverServiceInterface $createService,
        ReadDriverServiceInterface $readService
    )
    {
        $this->createService = $createService;
        $this->readService = $readService;
    }

    public function create($data, $hateos = true):array
    {
        if ($hateos) {
            $data = $this->createService->execute($data);
            $path = request()->path();
            $newData = $this->addHateosLinksToItems($data, $path);
            return $newData[0];
        }
        return $this->createService->execute($data);
    }

    public function read($id)
    {
        //return $this->readService->execute($id);
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function list(Criteria $criteria, $hateos = true):LengthAwarePaginator
    {
        $paginator = $this->readService->execute($criteria);
        if ($hateos && is_array($paginator->items())) {
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
     */
    public function count(Criteria $criteria): int
    {
        return $this->readService->count($criteria);
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