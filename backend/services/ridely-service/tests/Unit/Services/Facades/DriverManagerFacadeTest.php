<?php

namespace Tests\Unit\Services\Facades;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\DriverException;
use App\Exceptions\ServiceException;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use App\Services\Facades\DriverManagerFacade;
use App\Services\V1\DriverService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Mocks\Services\DriverManagerFacadeMock;
use Tests\Helpers\DriverHelper;
use Tests\Unit\UnitTestCase;

class DriverManagerFacadeTest extends UnitTestCase
{
    protected ListCriteria $criteria;

    protected DriverManagerFacade $facade;

    protected DriverManagerFacadeMock $mock;

    /**
     * @param LengthAwarePaginator $result
     * @return void
     */
    public function assertListResponse(LengthAwarePaginator $result): void
    {
// Assert paginator type
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);

        // Assert pagination meta
        $this->assertEquals(3, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(15, $result->perPage());
        $this->assertEquals('http://localhost', $result->path());

        // Assert items count
        $this->assertCount(3, $result->items());

        // Assert first item structure and values
        $first = $result->items()[0];
        $this->assertNotNull($first);
        $this->assertNotNull($first['id']);
        $this->assertArrayHasKey('_links', $first);

        // Assert Hateos links structure
        $links = $first['_links']->toArray();
        $this->assertArrayHasKey('self', $links);
        $this->assertArrayHasKey('update', $links);
        $this->assertArrayHasKey('replace', $links);
        $this->assertArrayHasKey('delete', $links);
        $this->assertEquals('GET', $links['self']['method']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = new DriverManagerFacadeMock();

        $driverService = $this->app->get(DriverService::class);
        $this->facade = new DriverManagerFacade($driverService);
    }
    public function testCreateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $data = DriverHelper::getDriverSample();

        $criteria = new CreateDriverCriteria($data);
        $driver = $this->facade->create($criteria);

        $this->assertEquals(1, $driver['id']);
        $this->assertEquals($data['name'], $driver['name']);
        $this->assertEquals($data['car']['license_plate'], $driver['car_license_plate']);

    }

    public function testUpdateSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped('Not implemented yet');
    }

    public function testListSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Driver::factory()->count(3)->create();

        $criteria = new ListCriteria([]);

        $result = $this->facade->list($criteria);
        $this->assertListResponse($result);

    }

    public function testListSuccessWithValidCriteria()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Driver::factory()->count(3)->create();

        $criteria = new ListCriteria(["fields" => "id, name"]);

        $result = $this->facade->list($criteria);
        $this->assertListResponse($result);

    }

    public function testListFailWithInvalidCriteria()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $expectedErrorMessage = 'The selected fields.0 is invalid.';
        $criteria = new ListCriteria(["fields" => "invalid_field"]);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::INVALID_REQUEST_PARAM->message($expectedErrorMessage));

        $this->facade->list($criteria);

    }

    public function testListFailWithInvalidLimitCriteria()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $expectedErrorMessage = "The limit field must not be greater than 100.";
        $criteria = new ListCriteria(["limit" => 1001]);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::INVALID_REQUEST_PARAM->message($expectedErrorMessage));

        $this->facade->list($criteria);

    }



    public function testReadSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->markTestSkipped('Not implemented yet');
    }

    public function testDeleteSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $driver = Driver::factory()->create();


        $result = $this->facade->delete($driver->id);

        $this->assertTrue($result);
    }

    public function testDeleteFailDriverNotFound()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::DRIVER_NOT_FOUND->message());


        $deleted = $this->facade->delete(999);
        $this->assertTrue($deleted);
    }
}
