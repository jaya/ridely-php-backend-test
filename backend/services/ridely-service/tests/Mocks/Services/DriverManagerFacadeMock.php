<?php

namespace Mocks\Services;

use App\Services\Facades\DriverManagerFacade;
use App\Services\V1\DriverService;
use App\Validators\DriverValidator;
use Mocks\AbstractMock;
use Mocks\Validators\DriverValidatorMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class DriverManagerFacadeMock extends AbstractMock
{
    public DriverService $driverService;

    public DriverValidator $validator;

    public function __construct()
    {
        parent::__construct();

        $this->validator = (new DriverValidatorMock())->getObjectWithMockDependencies();

        $this->driverService = new DriverService($this->validator);
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
        return new DriverManagerFacade($this->driverService);
    }
}