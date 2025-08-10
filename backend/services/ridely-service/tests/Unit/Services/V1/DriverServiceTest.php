<?php

namespace Tests\Unit\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RideStatusEnum;
use App\Exceptions\DriverException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\DriverCacheService;
use App\Services\Interfaces\DriverServiceInterface;
use App\Services\V1\DriverService;
use App\Validators\DriverValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Helpers\DriverHelper;
use Tests\Mocks\DriverMocks;
use Tests\Unit\UnitTestCase;

class DriverServiceTest extends UnitTestCase
{
    use DriverMocks;
    private ListCriteria $criteria;

    private DriverService $service;

    private DriverService $mock;


    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockDriverService();
        $this->service = $this->app->get(DriverServiceInterface::class);


    }

    public function testCreateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $fakeDriver = $this->mockDriverModelCreate();
        $data = $fakeDriver->toArray();

        $criteria = new CreateDriverCriteria($data);

        // Act
        $result = $this->service->create($criteria);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($data['name'], $result->name);
    }

    public function testCreateFail()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $expectedErrorMessage = 'The name field is required.';
        $data = DriverHelper::getDriverSample();
        $data['name'] = null;

        $criteria = new CreateDriverCriteria($data);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::INVALID_REQUEST->message($expectedErrorMessage));

        $this->service->create($criteria);


    }

    /**
     * @throws ServiceException
     */
    public function testAllWithCriteriaSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockDriverModelAllDrivers();

        $criteria = new ListCriteria([
            'fields' => ['id', 'name'],
            'order_by' => 'id',
            'sort_by' => 'asc',
            'offset' => 0,
            'limit' => 2,
        ]);

        // Act
        $results = $this->service->read($criteria);

        // Assert
        $this->assertCount(5, $results);
        $this->assertArrayHasKey('name', $results[0]);
    }


    public function testUpdateDriverNotFoundFail()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped("Not implemented yet");
    }

    public function testDeleteSuccess()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Assert
        $fakeDriver = $this->mockDriver();
        $this->driverModelMock->shouldReceive('findOrFail')->andReturn($fakeDriver);
        $fakeDriver->shouldReceive('delete')->andReturn(true);

        // Act
        $result = $this->service->delete($fakeDriver->id);

        // Assert
        $this->assertTrue($result);

    }

    public function testDeleteFailDriverNotFound()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );
        $id = 999;
        $this->driverModelMock->shouldReceive('findOrFail')->andReturn(null);

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage(DriverException::notFound(["id" => $id])->getMessage());


        $deleted = $this->service->delete($id);
        $this->assertTrue($deleted);
    }

}