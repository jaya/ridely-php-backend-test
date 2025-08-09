<?php

namespace Tests\Unit\Http\Controllers;

use App\Enums\ErrorMessagesEnum;
use App\Enums\RideEstimateStatusEnum;
use App\Enums\RideStatusEnum;
use App\Http\Middleware\ValidateKeycloakJwt;
use App\Models\Driver;
use App\Models\Ride;
use App\Models\RideEstimate;
use App\Services\JWTKeysService;
use App\Services\RideCacheService;
use Database\Seeders\DriverSeeder;
use Database\Seeders\PricingRulesSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Log;
use Tests\Helpers\LocationHelper;
use Tests\Helpers\TokenHelper;
use Tests\Unit\UnitTestCase;

class RideControllerTest extends UnitTestCase
{
    use RefreshDatabase;

    private $locationServiceUrl;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        $this->withoutMiddleware(ValidateKeycloakJwt::class);

        $this->locationServiceUrl = env('LOCATION_SERVICE_URL', 'https://nominatim.openstreetmap.org/search');

        $this->seed(DriverSeeder::class);
        $this->seed(PricingRulesSeeder::class);
    }

    public function testShowWithoutDriverSuccess()
    {
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $ride = Ride::create([
            'passenger_name' => 'Jane Smith',
            'passenger_email' => 'jane@example.com',
            'pick_up' => '123 Main St',
            'drop_off' => '456 Park Ave',
            'status' => RideStatusEnum::REQUESTED->value
        ]);


        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/rides/{$ride->id}");

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
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $driver = Driver::create([
            'name' => 'John Doe',
            'car_license_plate' => 'ABC123',
            'car_model' => 'Toyota Corolla',
            'car_color' => 'Blue',
            'available' => true
        ]);
        //TODO usar helper

        $ride = Ride::create([
            'passenger_name' => 'Jane Smith',
            'passenger_email' => 'jane@example.com',
            'pick_up' => '123 Main St',
            'drop_off' => '456 Park Ave',
            'status' => RideStatusEnum::REQUESTED->value
        ]);

        $ride->accept($driver);

        $rideId = $ride->id;


        $response = $this->withHeader('Authorization', "Bearer $token")
            ->get("/api/v1/rides/{$rideId}");

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
                'driver' => [
                    'name',
                    'car' => [
                        'license_plate',
                        'model',
                        'color',
                    ],
                ],
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

        // Mock the external calls
        $this->mockCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $ride = $this->createRideWithRideEstimation();
        $rideId = $ride->id;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/rides/$rideId/estimate-ride");

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

        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();

        $drivers = Driver::factory()->count(5)->create();
        $fakeDriver = $drivers[0];

        $data = [
            'pick_up' => LocationHelper::$pickUp,
            'drop_off' => LocationHelper::$dropOff,
            'passenger' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]
        ];
        // Mock do serviço de cache
        $rideCacheServiceMock = \Mockery::mock(RideCacheService::class);
        $rideCacheServiceMock
            ->shouldReceive('getNextAvailableDriver')
            ->once()
            ->andReturn($fakeDriver);

        // Injeta o mock no container do Laravel
        $this->app->instance(RideCacheService::class, $rideCacheServiceMock);


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
                'driver' => [
                    'name',
                    'car' => [
                        'license_plate',
                        'model',
                        'color',
                    ],
                    'available',
                ],
                'estimate' => [
                    'status',
                    'distance_km',
                    'duration_min',
                    'price_estimate',
                ],
            ],
        ]);
//
//        $dataObject = $response->json();
//        $data = $dataObject['data'];
//
//        $this->assertResponse($data);
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
    public function createRideWithRideEstimation(): Ride|Collection|Model
    {
        $ride = Ride::factory()->create([
            "pick_up" => LocationHelper::$pickUp,
            "drop_off" => LocationHelper::$dropOff,
        ]);
        $estimate = RideEstimate::create([
            'ride_id' => $ride->id,
            'status' => RideEstimateStatusEnum::PENDING,
        ]);

        $ride->estimate()->save($estimate);
        return $ride;
    }


}