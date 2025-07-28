<?php

namespace Mocks\Services\V1\Driver;

use App\Repositories\V1\DriverRepository;
use App\Services\V1\Driver\ReadDriverService;
use App\Validators\DriverValidator;
use Mocks\AbstractMock;
use Mocks\Repositories\V1\DriverRepositoryMock;
use Mocks\Validators\DriverValidatorMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class ReadDriverServiceMock extends AbstractMock
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
    public function getMock(): ReadDriverService&MockObject
    {
        return $this->createMock(ReadDriverService::class);
    }

    /**
     * @throws Exception
     */
    public function getObjectWithMockDependencies(): object
    {
        return new ReadDriverService($this->repository, $this->validator);
    }
}