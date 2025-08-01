<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use Exception;
use Firebase\JWT\JWK;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class JWTKeysService
{
    protected Client $client;
    protected string $realmUrl;
    const CACHE_KEY = 'keycloak_jwks';

    protected $cacheTtl = 3600; // Tempo de vida do cache em segundos (1 hora por padrão)

    public function __construct(Client $client = null)
    {
        if ($client == null) {
            $client = new Client([
                'timeout' => 5,
                'verify' => false,
            ]);
        }

        $this->client = $client;
        $this->realmUrl = rtrim(config('keycloak.realm_url'), '/');
    }

    /**
     * @throws ServiceException
     */
    public function getPublicKeys(): array
    {
        $jwks = Cache::remember(self::CACHE_KEY, $this->cacheTtl, function () {
            return $this->fetchJwk();
        });

        try {
            if (empty($jwks['keys'])) {
                throw new \Exception("No JWKS keys found in response.");
            }
            return JWK::parseKeySet(['keys' => $jwks['keys']]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw ServiceException::unableToRequestAuthPublicKey($e->getMessage(), ['url' => $this->realmUrl]);
        }


    }

    /**
     * @return array
     * @throws ServiceException
     */
    public function fetchJwk(): array
    {
        try {
            // Find OpenID
            $discoveryUrl = $this->realmUrl . '/.well-known/openid-configuration';
            $openidResponse = $this->client->get($discoveryUrl);
            $config = json_decode($openidResponse->getBody()->getContents(), true);

            if (!isset($config['jwks_uri'])) {
                throw new Exception("JWKS URI not found in OpenID config.");
            }

            // Get Jwks endpoint
            $jwksResponse = $this->client->get($config['jwks_uri']);
            return json_decode($jwksResponse->getBody()->getContents(), true);

        } catch (Exception $e) {
            throw ServiceException::unableToRequestAuthPublicKey($e->getMessage(), ['url' => $this->realmUrl]);
        } catch (GuzzleException $e) {
            throw ServiceException::unableToRequestAuthPublicKey($e->getMessage(), ['url' => $this->realmUrl]);
        }
    }


}