<?php

namespace Tests\Unit\Services\V1;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ServiceException;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Services\V1\DriverService;
use App\Validators\DriverValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $data = DriverHelper::getDriverSample();
        $data['id'] = null;

        $result = $this->service->create($data);

        $this->assertNotNull($result);
        $this->assertEquals($data['name'], $result->name);
    }

    public function testCreateFail()
    {
        $expectedErrorMessage = 'The name field is required.';
        $data = DriverHelper::getDriverSample();
        $data['name'] = null;


        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::INVALID_REQUEST->message($expectedErrorMessage));

        $this->service->create($data);


    }

    /**
     * @throws ServiceException
     */
    public function testAllWithCriteria()
    {
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
//    public function testDeleteDriverNotFound()
//    {
//
//        $this->expectException(RepositoryException::class);
//        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());
//
//        $this->repository->delete(999);
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