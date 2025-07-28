<?php

namespace Mocks\Repositories\V1;

use App\Repositories\V1\DriverRepository;
use Mocks\AbstractMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

class DriverRepositoryMock extends AbstractMock
{

    /**
     * @throws Exception
     */
    public function getMock(): DriverRepository&MockObject
    {
        return $this->createMock(DriverRepository::class);
    }

    public function getObjectWithMockDependencies(): object
    {
        return new DriverRepository();
    }
}