<?php

namespace Mocks\Services;

use App\Repositories\V1\DriverRepository;
use App\Services\Facades\DriverManagerFacade;
use App\Services\Interfaces\Driver\CreateDriverServiceInterface;
use App\Services\Interfaces\Driver\ReadDriverServiceInterface;
use Mocks\AbstractMock;
use Mocks\Repositories\V1\DriverRepositoryMock;
use Mocks\Services\V1\Driver\CreateDriverServiceMock;
use Mocks\Services\V1\Driver\ReadDriverServiceMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class DriverManagerFacadeMock extends AbstractMock
{
    public CreateDriverServiceInterface $createService;
    public ReadDriverServiceInterface $readService;

    public DriverRepository $repository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->repository = (new DriverRepositoryMock())->getMock();

        $createServiceMock = new CreateDriverServiceMock();
        $createServiceMock->repository = $this->repository;

        $this->createService = $createServiceMock->getObjectWithMockDependencies();

        $readServiceMock = new ReadDriverServiceMock();
        $readServiceMock->repository = $this->repository;

        $this->readService = $readServiceMock->getObjectWithMockDependencies();
    }

    /**
     * @throws Exception
     */
    public function getMock(): DriverManagerFacade&MockObject
    {
        return $this->createMock(DriverManagerFacade::class);
    }

    public function getObjectWithMockDependencies(): object
    {
        return new DriverManagerFacade($this->createService, $this->readService);
    }
}