<?php

namespace Tests\Unit\Validators;

use App\Validators\LocationValidator;
use Illuminate\Validation\ValidationException;
use Tests\Unit\UnitTestCase;

class LocationValidatorTest extends UnitTestCase
{
    protected LocationValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new LocationValidator();
    }
    public function testValidateAddressSuccess()
    {
        $result = $this->validator->validate('Rua Sergipe, 123');

        $this->assertTrue($result);
    }

    public function testValidateFailsOnEmptyAddress()
    {
        $result = $this->validator->validate('');

        $this->assertFalse($result);
        $this->assertInstanceOf(ValidationException::class, $this->validator->getException());
    }

    public function testValidateFailWithNullAddress()
    {
        $result = $this->validator->validate(null);

        $this->assertFalse($result);
        $this->assertInstanceOf(ValidationException::class, $this->validator->getException());
    }
}