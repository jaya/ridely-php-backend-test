<?php

namespace App\Services;

use App\Http\Criteria\Criteria;
use App\Services\Interfaces\Driver\CreateDriverServiceInterface;
use App\Services\Interfaces\Driver\ReadDriverServiceInterface;

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