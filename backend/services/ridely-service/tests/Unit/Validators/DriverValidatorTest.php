<?php

namespace Tests\Unit\Validators;

use App\Http\Criteria\Criteria;
use App\Validators\DriverValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class DriverValidatorTest extends UnitTestCase
{
    protected DriverValidator $validator;

    protected function setUp(): void {
        parent::setUp();
        $this->validator = new DriverValidator();
    }

    public function testValidateCreateReturnsTrueWithValidData()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $data = [
            'name' => 'Carlos',
            'car' => [
                'license_plate' => 'ABC1234',
                'model' => 'Fiat Uno',
                'color' => 'Vermelho',
            ],
            'available' => true,
        ];

        $result = $this->validator->validateCreate($data);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getInvalidDataDataProvider
     * @return void
     * @throws ValidationException
     */
    public function testValidateCreateReturnsFalseWithInvalidData($data)
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $result = $this->validator->validateCreate($data);
        $this->assertFalse($result);

        $this->expectException(ValidationException::class);
        throw $this->validator->getException();
    }

    public function testValidateUpdateReturnsTrueWithValidData()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $data = [
            'id' => '1',
            'name' => 'Carlos Souza',
            'car' => [
                'license_plate' => 'ABC1234',
                'model' => 'Fiat Uno',
                'color' => 'Vermelho',
            ],
            'available' => true,
        ];

        $result = $this->validator->validateUpdate($data);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getInvalidDataDataProvider
     * @return void
     * @throws ValidationException
     */
    public function testValidateUpdateReturnsFalseWithInvalidData($data)
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $result = $this->validator->validateCreate($data);
        $this->assertFalse($result);

        $this->expectException(ValidationException::class);
        throw $this->validator->getException();
    }

    /**
     * @dataProvider getValidDataForValidateReadDataProvider
     * @param Criteria $criteria
     * @return void
     */
    public function testValidateReadReturnsTrueWithValidData(Criteria $criteria)
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );
        $result = $this->validator->validateRead($criteria);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getInvalidDataForValidateReadDataProvider
     * @param Criteria $criteria
     * @return void
     */
    public function testValidateReadReturnsFalseWithInvalidData(Criteria $criteria)
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $result = $this->validator->validateRead($criteria);
        $this->assertFalse($result);

        $this->expectException(ValidationException::class);
        throw $this->validator->getException();
    }

    public function getInvalidDataDataProvider(): array
    {
        return [
            [null],
            [[]],
            [[
                'name' => '',
                'car' => [
                    'license_plate' => '',
                    'model' => 'Fiat Uno',
                    'color' => 'Vermelho',
                ],
                'available' => 'not_boolean',
            ]]
        ];
    }
    public static function getValidDataForValidateReadDataProvider(): array
    {
        return [
            'basic valid criteria' => [
                new Criteria([
                    'page' => 0,
                    'limit' => 10,
                    'order_by' => 'name',
                    'sort_by' => 'asc',
                    'fields' => 'name,available'
                ])
            ],
            'partial criteria (only required)' => [
                new Criteria([
                    'limit' => 5
                ])
            ],
            'criteria with only sorting' => [
                new Criteria([
                    'order_by' => 'created_at',
                    'sort_by' => 'desc'
                ])
            ]
        ];
    }

    public static function getInvalidDataForValidateReadDataProvider(): array
    {
        return [
            'negative limit and page' => [
                new Criteria([
                    'page' => -1,
                    'limit' => -10
                ])
            ],
            'invalid sort_by value' => [
                new Criteria([
                    'order_by' => 'name',
                    'sort_by' => 'random'
                ])
            ],
            'limit too high' => [
                new Criteria([
                    'limit' => 9999
                ])
            ],
            'fields not string or array' => [
                new Criteria([
                    'fields' => 123 // em vez de string/array
                ])
            ]
        ];
    }

}