<?php

namespace Tests\Unit\Services\V1\Driver;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ServiceException;
use App\Http\Criteria\Criteria;
use App\Services\V1\Driver\ReadDriverService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mocks\Services\V1\Driver\ReadDriverServiceMock;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Helpers\DriverHelper;
use Tests\Unit\UnitTestCase;

// TODO revisar os nomes dos testes
class ReadDriverServiceTest extends UnitTestCase
{
    protected Criteria $criteria;

    protected ReadDriverService $service;

    protected ReadDriverServiceMock $mock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = new ReadDriverServiceMock();
    }

    /**
     * @throws ServiceException
     * @throws Exception
     */
    public function testExecuteWithValidCriteriaReturnsDrivers()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $drivers = collect(DriverHelper::getDriversListSample());

        $paginator = new LengthAwarePaginator(
            items: $drivers,
            total: $drivers->count(),
            perPage: 15,
            currentPage: 1,
            options: [
                'path' => '/api/v1/drivers',
                'query' => []
            ]
        );

        $this->criteria = new Criteria([]);

        $this->mock->repository
            ->expects($this->once())
            ->method('all')
            ->with($this->criteria)
            ->willReturn($paginator);

        $this->service = $this->mock->getObjectWithMockDependencies();
        $result = $this->service->execute($this->criteria);
        $items = $result->items();

        $this->assertEquals($drivers->toArray(), $items);
    }

    public function testExecuteWithInvalidCriteriaThrowsServiceException()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $expectedErrorMessage = 'The selected fields.0 is invalid.';
        $this->criteria = new Criteria(["fields" => "invalid_field"]);
        $this->service = $this->mock->getObjectWithMockDependencies();

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ErrorMessagesEnum::INVALID_REQUEST_PARAM->message($expectedErrorMessage));

        $this->service->execute($this->criteria);
    }

    public function testValidateReturnsTrueWhenValid()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->criteria = new Criteria(["fields" => "id, name"]);
        $this->service = $this->mock->getObjectWithMockDependencies();

        $this->assertTrue($this->service->validate($this->criteria));
    }

    public function testValidateReturnsFalseWhenInvalid()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $expectedErrorMessage = "The limit field must not be greater than 100.";
        $this->criteria = new Criteria(["limit" => 1001]);
        $this->service = $this->mock->getObjectWithMockDependencies();

        $this->assertFalse($this->service->validate($this->criteria));

        $this->assertInstanceOf(ValidationException::class, $this->service->getException());
        $this->assertEquals($expectedErrorMessage, $this->service->getException()->getMessage());

    }
}
