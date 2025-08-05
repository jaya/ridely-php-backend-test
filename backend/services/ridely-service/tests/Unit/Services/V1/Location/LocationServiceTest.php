<?php

namespace Tests\Unit\Services\V1\Location;

use App\Services\V1\Location\LocationService;
use App\Validators\LocationValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\Helpers\LocationHelper;
use Tests\Unit\UnitTestCase;

class LocationServiceTest extends UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new LocationValidator();
    }
    public function testExecuteWithValidApiResponseReturnsLatLon()
    {
        $address = LocationHelper::$pickUp;

        $this->mockCalls(LocationHelper::getDatasourceDataForDropOffSuccessResponse());

        $service = new LocationService($this->validator);

        $result = $service->execute($address);

        $this->assertIsArray($result);
        $this->assertEquals(-10.8819106, $result['lat']);
        $this->assertEquals(-37.0808969, $result['lon']);
    }

    public function testExecuteWithEmptyApiResponseReturnsNull()
    {
        $this->mockCalls([]);

        $service = new LocationService($this->validator);

        $result = $service->execute('Some address');

        $this->assertNull($result);
    }

    public function testExecuteWithMissingLatOrLonReturnsNull()
    {
        $this->mockCalls([['lat' => '-10.9472']]);

        $service = new LocationService($this->validator);

        $result = $service->execute('Some address');

        $this->assertNull($result);
    }

    public function testValidateWithValidAddressReturnsTrue()
    {
        $validator = $this->createMock(LocationValidator::class);
        $validator->method('validate')->willReturn(true);

        $service = new LocationService($validator);

        $this->assertTrue($service->validate('Aracaju, SE'));
    }

    public function testValidateFailWithNullAddress()
    {
        $exception = $this->createMock(ValidationException::class);

        $validator = $this->createMock(LocationValidator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getException')->willReturn($exception);

        $service = new LocationService($validator);

        $this->assertFalse($service->validate(''));
        $this->assertInstanceOf(ValidationException::class, $service->getException());
    }


    /**
     * @param array $fakeResponse
     * @return void
     */
    public function mockCalls(array $fakeResponse, int $status = 200): void
    {
        Http::fake([
            '*' => Http::response($fakeResponse, $status),
        ]);
    }


}
