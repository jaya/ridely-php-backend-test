<?php

namespace Tests\Unit\Services;

use App\Exceptions\ServiceException;
use App\Services\JWTKeysService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Unit\UnitTestCase;

class JWTKeysServiceTest extends UnitTestCase
{
    protected JWTKeysService $service;

    protected Client $clientMock;

    /**
     * @throws Exception
     */
    public function setUp(): void {
        parent::setUp();

        // Limpa cache antes
        Cache::forget(JWTKeysService::CACHE_KEY);
        // Mock
        $this->clientMock = $this->createMock(Client::class);

        $this->service = new JWTKeysService($this->clientMock);


    }

    public function testGetPublicKeySuccess() {

        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $this->mockCalls();

        $keys = $this->service->getPublicKeys();
        $this->assertIsArray($keys);
        $this->assertTrue(count($keys) > 0);
    }

    /**
     * @return void
     */
    public function mockCalls(): void
    {
        $discoveryJson = File::get(base_path('tests/Datasources/http/keycloak/discovery.json'));
        $jwksJson = File::get(base_path('tests/Datasources/http/keycloak/jwks.json'));

        $this->clientMock
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                new Response(200, [], $discoveryJson),
                new Response(200, [], $jwksJson)
            );
    }
}
