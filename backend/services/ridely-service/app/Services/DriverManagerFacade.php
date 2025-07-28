<?php

namespace App\Services;

use App\Http\Criteria\Criteria;
use App\Services\Interfaces\Driver\CreateDriverService;
use App\Services\Interfaces\Driver\ReadDriverService;

class DriverManagerFacade
{
    protected CreateDriverService $createService;
    protected ReadDriverService $readService;
    public function __construct(
        CreateDriverService $createService,
        ReadDriverService $readService
    )
    {
        $this->createService = $createService;
        $this->readService = $readService;
    }

    public function create($data)
    {
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

    public function list(Criteria $criteria)
    {
        return $this->readService->execute($criteria);
    }
}