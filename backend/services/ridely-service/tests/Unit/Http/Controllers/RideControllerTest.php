<?php

namespace Tests\Unit\Http\Controllers;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RideEstimateStatusEnum;
use App\Enums\RideStatusEnum;
use App\Models\Driver;
use App\Models\PricingRule;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\JWTKeysService;
use App\Services\RideCacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Tests\Helpers\DriverHelper;
use Tests\Helpers\LocationHelper;
use Tests\Helpers\PricingRuleHelper;
use Tests\Helpers\RideEstimateHelper;
use Tests\Helpers\RideHelper;
use Tests\Helpers\TokenHelper;
use Tests\Unit\UnitTestCase;

class RideControllerTest extends UnitTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->locationServiceUrl = env('LOCATION_SERVICE_URL', 'https://nominatim.openstreetmap.org/search');

        $this->mockRideCacheService();
        $this->mockRideService($this->rideCacheServiceMock);
        $locationService = $this->mockLocationService($this->locationServiceUrl);
        $this->mockRideEstimateService($locationService);

    }

    public function testShowWithoutDriverSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $sample = RideHelper::getRideSample();
        $rideId = $sample['id'];

        $fakeRide = $this->createModelMockWithData(Ride::class, $sample);

        $this->rideModelMock->shouldReceive('findRide')
            ->once()
            ->andReturn($fakeRide);

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/rides/{$rideId}");


        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                'id',
                'status',
                'pick_up',
                'drop_off',
            ],
        ]);

        $dataObject = $response->json();
        $data = $dataObject['data'];

        $this->assertResponse($data);
    }

    public function testShowWithDriverSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $driverSample = DriverHelper::getDriverSample();
        $driverSample['available'] = true;
        $fakeDriver = $this->createModelMockWithData(Driver::class, $driverSample);

        $sample = RideHelper::getRideSample();
        $sample['status'] = RideStatusEnum::REQUESTED->value;
        $fakeRide = $this->createModelMockWithData(Ride::class, $sample);

        $this->rideModelMock->shouldReceive('findRide')
            ->once()
            ->andReturn($fakeRide);

        $fakeRide->accept($fakeDriver);
        $rideId = $sample['id'];

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/rides/{$rideId}");

        // Assert
        $response->assertStatus(200);
        //Only assert the full body of the response in the integration tests
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                'id',
                'status',
                'pick_up',
                'drop_off',
            ],
        ]);

        $dataObject = $response->json();
        $data = $dataObject['data'];

        $this->assertResponse($data);

    }

    /**
     * Google maps reference:
     * 10,1 km
     * 27 min
     * @link https://www.google.com/maps/dir/Av.+Gen.+Eucl%C3%ADdes+Figueiredo,+65+-+Dom+Luciano,+Aracaju+-+SE,+49070-523/Av.+Gov.+Paulo+Barreto+de+Menezes,+25+-+Farol%C3%A2ndia,+Aracaju+-+SE/@-10.9043079,-37.1047231,13z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x7054cd3611c4121:0x6bc9edf5b93a523!2m2!1d-37.0773645!2d-10.8773348!1m5!1m1!1s0x71ab386c59b8259:0x25d93ef731a4380!2m2!1d-37.0435827!2d-10.9254996!3e0?entry=ttu&g_ep=EgoyMDI1MDczMC4wIKXMDSoASAFQAw%3D%3D
     * @return void
     */
    public function testEstimateRideSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();


        $fakeRide = $this->createRideWithRideEstimation();

        $fakeRule = $this->createPricingRule();

        $this->rideModelMock->shouldReceive('findRide')
            ->once()
            ->andReturn($fakeRide);

        $this->rideEstimateModelMock->shouldReceive('findRideEstimate')
            ->once()
            ->andReturn($fakeRide->estimate);

        $this->pricingRuleModelMock->shouldReceive('filterRuleBasedOnTime')
            ->withAnyArgs()
            ->once()
            ->andReturn($fakeRule);

        $rideId = $fakeRide->id;

        // Act
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/rides/$rideId/estimate-ride");

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                'distance_km',
                'duration_min',
                'price_estimate',
            ],
        ]);

        $dataObject = $response->json();
        $data = $dataObject['data'];

        $this->assertIsNumeric($data['distance_km']);
        $this->assertIsNumeric($data['duration_min']);
        $this->assertIsNumeric($data['price_estimate']);
    }

    public function testEstimateRideFailWithInvalidRideId()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Mock the external calls
        $this->mockCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $rideId = 999;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/rides/${rideId}/estimate-ride");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'params'
        ]);

        $data = $response->json();
        $this->assertFalse($data['success']);
        $this->assertEquals(ErrorMessagesEnum::RIDE_NOT_FOUND->label(), $data['label']);
    }


    public function testRequestDriverSuccess()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        // Arrange
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $sample = DriverHelper::getDriverSample();
        $fakeDriver = $this->createModelMockWithData(Driver::class, $sample);

        $data = [
            'pick_up' => LocationHelper::$pickUp,
            'drop_off' => LocationHelper::$dropOff,
            'passenger' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]
        ];


        $this->rideCacheServiceMock
            ->shouldReceive('getNextAvailableDriver')
            ->once()
            ->andReturn($fakeDriver);

        $fakeRide = $this->createRideWithRideEstimation();
        $fakeRide->status = null;
        $fakeRide->estimate->shouldReceive('create')->once()
            ->andReturn($fakeRide->estimate);

        $this->rideModelMock->shouldReceive('create')
            ->once()
            ->andReturn($fakeRide);


        $response = $this->withHeader('Authorization', "Bearer $token")
            ->post("/api/v1/rides/request-driver", $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                'id',
                'status',
                'pick_up',
                'drop_off',
            ],
        ]);
    }

    private function mockTokenValidation(): void
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $parsedKeys = TokenHelper::getParseKeySet();

        $mockKeysService = $this->createMock(JWTKeysService::class);
        $mockKeysService->expects($this->any())
            ->method('getPublicKeys')
            ->willReturn($parsedKeys);

        $fakeToken = TokenHelper::getFakeTokenAsObject();

        $mockKeysService->expects($this->any())
            ->method('decodeToken')
            ->withAnyParameters()
            ->willReturn($fakeToken);

        $this->app->instance(JWTKeysService::class, $mockKeysService);

    }

    private function mockCalls()
    {
        LocationHelper::mockCall($this->locationServiceUrl, LocationHelper::$pickUp, LocationHelper::getDatasourceDataForPickUpSuccessResponse());
        LocationHelper::mockCall($this->locationServiceUrl, LocationHelper::$dropOff, LocationHelper::getDatasourceDataForDropOffSuccessResponse());
    }




    private function mockFailCalls()
    {
        LocationHelper::mockCall($this->locationServiceUrl, "invalid", []);
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function assertResponse(mixed $data): void
    {
        $this->assertIsInt($data['id']);
        $this->assertIsString($data['status']);
        $this->assertIsString($data['pick_up']);
        $this->assertIsString($data['drop_off']);

        // Verifica driver se existir
        if (isset($data['driver'])) {
            if (!is_null($data['driver'])) {
                $this->assertIsArray($data['driver']);
                $this->assertArrayHasKey('name', $data['driver']);
                $this->assertIsString($data['driver']['name']);

                $this->assertArrayHasKey('car', $data['driver']);
                $this->assertIsArray($data['driver']['car']);
                $this->assertIsString($data['driver']['car']['license_plate']);
                $this->assertIsString($data['driver']['car']['model']);
                $this->assertIsString($data['driver']['car']['color']);
            } else {
                $this->assertNull($data['driver']);
            }
        }

    }

    /**
     * @return Ride|Collection|Model
     */
    public function createRideWithRideEstimation(RideStatusEnum $statusEnum = null): Ride|Collection|Model
    {
        $fakeRideEstimate = $this->createRideEstimate();

        $sample = RideHelper::getRideSample();
        $sample['status'] = $statusEnum ? $statusEnum->value : null;
        $fakeRide = $this->createModelMockWithData(Ride::class, $sample);

        $fakeRide->shouldReceive('estimate')
            ->andReturn($fakeRideEstimate);
        $fakeRide->estimate = $fakeRideEstimate;

        return $fakeRide;
    }

    private function createPricingRule()
    {
        $ruleSample = PricingRuleHelper::getPricingRuleSample();
        $fakeRule = $this->createModelMockWithData(PricingRule::class, $ruleSample);
        return $fakeRule;

    }

    /**
     * @return mixed
     */
    public function createRideEstimate(): mixed
    {
        $rideEstimateSample = RideEstimateHelper::getRideEstimateSample();
        $rideEstimateSample['status'] = RideEstimateStatusEnum::PENDING->value;
        $fakeRideEstimate = $this->createModelMockWithData(RideEstimate::class, $rideEstimateSample);
        return $fakeRideEstimate;
    }


}