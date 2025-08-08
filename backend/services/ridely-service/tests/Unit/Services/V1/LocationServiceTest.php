<?php

namespace Tests\Unit\Services\V1;

use App\Exceptions\RideException;
use App\Models\PricingRule;
use App\Services\V1\LocationService;
use App\Validators\LocationValidator;
use Carbon\Carbon;
use Database\Seeders\PricingRulesSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Tests\Helpers\LocationHelper;
use Tests\Unit\UnitTestCase;

class LocationServiceTest extends UnitTestCase
{

    protected LocationService $service;
    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new LocationValidator();
        $this->pricingRule = $this->app->make(PricingRule::class);
        $this->service = new LocationService($this->pricingRule, $this->validator);

        $this->seed(PricingRulesSeeder::class);
    }
    public function testGetCoordinatesFromAddressWithSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $address = LocationHelper::$pickUp;

        $this->mockCalls(LocationHelper::getDatasourceDataForDropOffSuccessResponse());



        $result = $this->service->getCoordinatesFromAddress($address);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result['lat']);
        $this->assertNotEmpty($result['lon']);
//        $this->assertEquals(-10.8819106, $result['lat']);
//        $this->assertEquals(-37.0808969, $result['lon']);
    }

    public function testGetCoordinatesFromAddressUsingCacheWithSuccess()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $address = LocationHelper::$pickUp;
        $lat = -10.8819106;
        $lon = -37.0808969;

        $this->mockCalls(LocationHelper::getDatasourceDataForDropOffSuccessResponse());

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([
                'lat' => $lat,
                'lon' => $lon,
            ]);
        
        $result = $this->service->getCoordinatesFromAddress($address);

        $this->assertIsArray($result);

        $this->assertEquals($lat, $result['lat']);
        $this->assertEquals($lon, $result['lon']);
    }

    public function testGetCoordinatesFromAddressWithEmptyApiResponseReturnsNull()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->mockCalls([]);


        $result = $this->service->getCoordinatesFromAddress('Some address');
        if (LocationService::MOCK_RESPONSE) {
            $this->assertNotEmpty($result['lat']);
            $this->assertNotEmpty($result['lon']);
        } else {
            $this->assertNull($result);
        }

    }

    public function testGetCoordinatesFromAddressWithMissingLatOrLonReturnsNull()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->mockCalls([['lat' => '-10.9472']]);


        $result = $this->service->getCoordinatesFromAddress('Some address');

        if (LocationService::MOCK_RESPONSE) {
            $this->assertNotEmpty($result['lat']);
            $this->assertNotEmpty($result['lon']);
        } else {
            $this->assertNull($result);
        }
    }

    public function testValidateWithValidAddressReturnsTrue()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $validator = $this->createMock(LocationValidator::class);
        $validator->method('validate')->willReturn(true);

        $service = new LocationService($this->pricingRule, $validator);

        $this->assertTrue($service->validate('Aracaju, SE'));
    }

    public function testValidateFailWithNullAddress()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $exception = $this->createMock(ValidationException::class);

        $validator = $this->createMock(LocationValidator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getException')->willReturn($exception);

        $service = new LocationService($this->pricingRule, $validator);

        $this->assertFalse($service->validate(''));
        $this->assertInstanceOf(ValidationException::class, $service->getException());
    }


    public function testCalculateArea()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $lat1 = -10.9472;
        $lon1 = -37.0731;
        $lat2 = -10.9121;
        $lon2 = -37.0719;

        $distance = $this->service->calculateArea($lat1, $lon1, $lat2, $lon2);

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(0, $distance);
    }

    public function testCalculateDurationTimeDuringRushHour()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 8, 0)); // 8h da manhã

        $distanceKm = 15;
        $duration = $this->service->calculateDurationTime($distanceKm);

        // 30 km/h → 15 km → 0.5h = 30min
        $this->assertEquals(30, $duration);
    }

    public function testCalculateDurationTimeOutsideRushHour()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 14, 0)); // 14h

        $distanceKm = 45;
        $duration = $this->service->calculateDurationTime($distanceKm);

        // 45 km/h → 45 km → 1h = 60min
        $this->assertEquals(60, $duration);
    }

    /**
     * @throws RideException
     */
    public function testCalculatePriceWithFlag2()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 6, 30)); // antes das 7h

        $distanceKm = 10;
        $price = $this->service->calculatePrice($distanceKm);

        $this->assertEquals(41.00, $price);
    }

    public function testCalculatePriceWithRushHour()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 8, 0)); // 8h

        $distanceKm = 10;
        $price = $this->service->calculatePrice($distanceKm);

        $this->assertEquals(35.00, $price);
    }

    public function testCalculatePriceWithDefaultRule()
    {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 14, 0)); // 14h

        $distanceKm = 5;
        $price = $this->service->calculatePrice($distanceKm);

        $this->assertEquals(16.50, $price);
    }

    public function testCalculatePriceFailsIfNoRuleFound()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        Carbon::setTestNow(Carbon::create(2025, 8, 5, 14, 0));

        $pricingRuleMock = $this->createMock(PricingRule::class);
        $pricingRuleMock->expects($this->once())
            ->method('filterRuleBasedOnTime')
            ->withAnyParameters()
            ->willReturn(null);


        $this->expectException(RideException::class);

        $distanceKm = 5;
        $service = new LocationService($pricingRuleMock, $this->validator);
        $service->calculatePrice($distanceKm);
    }


    /**
     * @param array $fakeResponse
     * @param int $statusCode
     * @return void
     */
    public function mockCalls(array $fakeResponse, int $statusCode = 200): void
    {
        LocationHelper::mockCall('*', null, $fakeResponse, $statusCode);
    }
}
