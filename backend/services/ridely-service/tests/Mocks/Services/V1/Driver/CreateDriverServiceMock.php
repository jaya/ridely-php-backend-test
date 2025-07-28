<?php

namespace Mocks\Services\V1\Driver;

use App\Repositories\V1\DriverRepository;
use App\Services\V1\Driver\CreateDriverService;
use App\Validators\DriverValidator;
use Mocks\AbstractMock;
use Mocks\Repositories\V1\DriverRepositoryMock;
use Mocks\Validators\DriverValidatorMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class CreateDriverServiceMock extends AbstractMock
{

    public DriverRepository $repository;
    public DriverValidator $validator;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository = (new DriverRepositoryMock())->getMock();
        $this->validator = (new DriverValidatorMock())->getObjectWithMockDependencies();
    }

    /**
     * @throws Exception
     */
    public function getMock(): CreateDriverService&MockObject
    {
        return $this->createMock(CreateDriverService::class);
    }

    /**
     * @throws Exception
     */
    public function getObjectWithMockDependencies(): object
    {
        return new CreateDriverService($this->repository, $this->validator);
    }
}