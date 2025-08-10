<?php

namespace Tests\Unit\Http\Controllers;

use App\Converters\DriverConverter;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\DriverCacheService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Services\V1\DriverService;
use App\Validators\DriverValidator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Tests\Helpers\DriverHelper;
use Tests\Helpers\TokenHelper;
use Tests\Unit\UnitTestCase;

class DriverControllerTest extends UnitTestCase
{

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
        $sampleList = DriverHelper::getDriversModelListSample();
        $paginator =  new LengthAwarePaginator(
            $sampleList,
            count($sampleList),
            16,
            1,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        $this->driverModelMock->shouldReceive('allDrivers')
            ->withAnyArgs()
            ->once()
            ->andReturn($paginator);

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
        $sample = DriverHelper::getDriverSample();
        $fakeDriver = $this->createModelMockWithData(Driver::class, $sample);

        $this->driverModelMock->shouldReceive('create')
            ->once()
            ->andReturn($fakeDriver);

        $token = TokenHelper::getFakeToken();
        unset($sample['id']);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")->post('/api/v1/drivers', $sample);

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