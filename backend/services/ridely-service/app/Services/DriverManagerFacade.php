<?php

namespace App\Services;

use App\Http\Criteria\Criteria;
use App\Services\Interfaces\Driver\CreateDriver;
use App\Services\Interfaces\Driver\ReadDriver;

class DriverManagerFacade
{
    protected CreateDriver $createService;
    protected ReadDriver $readService;
    public function __construct(
        CreateDriver $createService,
        ReadDriver $readService
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