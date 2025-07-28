<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class UnitTestCase extends BaseTestCase
{
//    use ProphecyTrait;
    use CreatesApplication;

    /**
     * Patch a object attribute
     * @param object $instance
     * @param string $property
     * @param $value
     * @return void
     */
    public function patchObject(object $instance, string $property, $value): void
    {
        $reflection = new \ReflectionClass($instance);
        try {
            $property = $reflection->getProperty($property);
            $property->setAccessible(true);
            $property->setValue($instance, $value);
        } catch (\ReflectionException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
