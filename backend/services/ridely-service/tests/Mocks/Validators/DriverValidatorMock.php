<?php

namespace Mocks\Validators;

use App\Validators\DriverValidator;
use Mocks\AbstractMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class DriverValidatorMock extends AbstractMock
{

    /**
     * @throws Exception
     */
    public function getMock(): DriverValidator&MockObject
    {
        return $this->createMock(DriverValidator::class);
    }

    public function getObjectWithMockDependencies(): object
    {
        return new DriverValidator();
    }
}