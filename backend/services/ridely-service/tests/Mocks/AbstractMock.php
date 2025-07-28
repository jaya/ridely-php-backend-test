<?php

namespace Mocks;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractMock extends TestCase
{
    public function __construct()
    {
        parent::__construct(self::class);
    }

    /**
     * @return MockObject;
     */
    abstract public function getMock(): MockObject;

    /**
     * @return object
     */
    abstract public function getObjectWithMockDependencies(): object;
}