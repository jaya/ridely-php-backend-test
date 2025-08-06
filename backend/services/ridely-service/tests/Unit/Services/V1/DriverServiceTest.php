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
use App\Services\V1\DriverService;
use App\Validators\DriverValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Helpers\DriverHelper;
use Tests\Unit\UnitTestCase;

class DriverServiceTest extends UnitTestCase
{
    use RefreshDatabase;
    private ListCriteria $criteria;

    private DriverService $service;

    private DriverService $mock;


    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $validator = new DriverValidator();
        $this->mock = $this->createMock(DriverService::class);
        $this->service = new DriverService($validator);
    }

    public function testCreateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $data = DriverHelper::getDriverSample();
        $data['id'] = null;

        $criteria = new CreateDriverCriteria($data);

        $result = $this->service->create($criteria);

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
    public function testAllWithCriteria()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Driver::factory()->count(3)->create();

        $criteria = new ListCriteria([
            'fields' => ['id', 'name'],
            'order_by' => 'id',
            'sort_by' => 'asc',
            'offset' => 0,
            'limit' => 2,
        ]);

        $results = $this->service->read($criteria);

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('name', $results[0]);
    }


//    public function testUpdateDriverNotFound()
//    {
//
//        $this->expectException(RepositoryException::class);
//        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());
//
//        $this->repository->update(999, ['name' => 'Test']);
//    }
//
    public function testDeleteSuccess()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $driver = Driver::factory()->create();

        $deleted = $this->service->delete($driver->id);
        $this->assertTrue($deleted);
    }

    public function testDeleteFailDriverNotFound()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());


        $deleted = $this->service->delete(999);
        $this->assertTrue($deleted);
    }

//    public function testDeleteFailDriverHasDependencies()
//    {
//        Log::info(
//            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
//        );
//
//        self::markTestSkipped("Test needs to be fixed, as it is not working as expected");
//
//        $this->expectException(DriverException::class);
//        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());
//
//        $driver = Driver::factory()->create();
//        $ride = Ride::factory()->create();
//        $ride->status = RideStatusEnum::REQUESTED;
//        $ride->accept($driver);
//
//
//        $deleted = $this->service->delete($driver->id);
//        $this->assertTrue($deleted);
//    }
//
//    public function testFindSuccess()
//    {
//        $driver = Driver::factory()->create();
//
//        $result = $this->repository->find($driver->id);
//
//        $this->assertEquals($driver->id, $result->id);
//    }
//
//    public function testFindNotFound()
//    {
//        $this->expectException(RepositoryException::class);
//        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());
//
//        $this->repository->find(999);
//    }
}