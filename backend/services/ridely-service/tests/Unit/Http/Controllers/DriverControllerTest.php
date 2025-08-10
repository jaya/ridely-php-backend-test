<?php

namespace Tests\Unit\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Tests\Helpers\TokenHelper;
use Tests\Mocks\DriverMocks;
use Tests\Unit\UnitTestCase;

class DriverControllerTest extends UnitTestCase
{

    use DriverMocks;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockDriverService();
    }

    /**
     * Test the index method of the DriverController.
     *
     * @return void
     */
    public function testListSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockDriverModelAllDrivers();

        $token = TokenHelper::getFakeToken();

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")->get('/api/v1/drivers');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                '*' => ['id',
                    'name',
                    'car' => [
                        'license_plate',
                        'model',
                        'color',
                    ],
                    'available',
                ]
            ],
        ]);
    }

    public function testCreateDriverSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $fakeDriver = $this->mockDriverModelCreate();
        $data = $fakeDriver->toArray();

        $token = TokenHelper::getFakeToken();
        unset($data['id']);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/v1/drivers', $data);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                'id',
                'name',
                'car' => [
                    'license_plate',
                    'model',
                    'color',
                ],
                'available',

            ],
        ]);

        $dataObject = $response->json();
        $data = $dataObject['data'];

        $this->assertEquals($fakeDriver->id, $data['id']);

    }

}