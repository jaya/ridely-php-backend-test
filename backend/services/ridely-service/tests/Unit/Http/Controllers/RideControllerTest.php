<?php

namespace Tests\Unit\Http\Controllers;

use App\Enums\ErrorMessagesEnum;
use App\Http\Middleware\ValidateKeycloakJwt;
use App\Services\JWTKeysService;
use Database\Seeders\PricingRulesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
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

        $this->seed(PricingRulesSeeder::class);
    }

    /**
     * Google maps reference:
     * 10,1 km
     * 27 min
     * @link https://www.google.com/maps/dir/Av.+Gen.+Eucl%C3%ADdes+Figueiredo,+65+-+Dom+Luciano,+Aracaju+-+SE,+49070-523/Av.+Gov.+Paulo+Barreto+de+Menezes,+25+-+Farol%C3%A2ndia,+Aracaju+-+SE/@-10.9043079,-37.1047231,13z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x7054cd3611c4121:0x6bc9edf5b93a523!2m2!1d-37.0773645!2d-10.8773348!1m5!1m1!1s0x71ab386c59b8259:0x25d93ef731a4380!2m2!1d-37.0435827!2d-10.9254996!3e0?entry=ttu&g_ep=EgoyMDI1MDczMC4wIKXMDSoASAFQAw%3D%3D
     * @return void
    */
    public function testEstimateRideSuccess() {


        // Mock the external calls
        $this->mockCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();
        $data = [
            "pick_up" => LocationHelper::$pickUp,
            "drop_off" => LocationHelper::$dropOff,
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson("/api/v1/rides/estimate-ride", $data);

        $response->assertStatus(200);
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

    public function testEstimateRideFailWithEmptyParams() {

        // Mock the external calls
        $this->mockCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();
        $data = [
            "pick_up" => "",
            "drop_off" => "",
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/rides/estimate-ride", $data);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'params'
        ]);

        $data = $response->json();
        $this->assertFalse($data['success']);
        $this->assertEquals(ErrorMessagesEnum::INVALID_REQUEST_PARAM->label(), $data['label']);
    }

    public function testEstimateRideFailWithInvalidParams() {


        // Mock the external calls
        $this->mockFailCalls();
        $this->mockTokenValidation();
        $token = TokenHelper::getFakeToken();
        $data = [
            "pick_up" => "invalid",
            "drop_off" => "invalid",
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/v1/rides/estimate-ride", $data);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'params'
        ]);

        $data = $response->json();
        $this->assertFalse($data['success']);
        $this->assertEquals(ErrorMessagesEnum::RIDE_UNABLE_TO_LOCATE_ADDRESS_DATA->label(), $data['label']);
    }

    private function mockTokenValidation(): void
    {
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
//        $q = rawurlencode(LocationHelper::$pickUp);
//        $url = "$this->locationServiceUrl?format=jsonv2&q=$q*";
//        Http::fake([
//            $url => Http::response(LocationHelper::getDatasourceDataForPickUpSuccessResponse())
//
//        ]);
//        $q = rawurlencode(LocationHelper::$dropOff);
//        $url = "$this->locationServiceUrl?format=jsonv2&q=$q*";
//        Http::fake([
//            $url => Http::response(LocationHelper::getDatasourceDataForDropOffSuccessResponse())
//        ]);

    }

    private function mockFailCalls()
    {
        LocationHelper::mockCall($this->locationServiceUrl, "invalid", []);
    }






}