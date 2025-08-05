<?php

namespace Tests\Helpers;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\File;

class TokenHelper
{
    public static function getFakeToken():string
    {
        $payload = static::getFakeTokenPayload();
        // Info: RS256 will requires a private-key.pem
        //return JWT::encode($payload, "private-key-mocked", 'RS256');
        return JWT::encode($payload, 'fake-key-for-test', 'HS256');
    }

    public static function getJwtKeys(): array
    {
        $jwksJson = File::get(base_path('tests/Datasources/http/keycloak/jwks.json'));
        $jwks = json_decode($jwksJson, true);
        return $jwks;
    }

    /**
     * @return array
     */
    public static function getFakeTokenPayload(): array
    {
        $payload = [
            'iss' => config('keycloak.issuer'),
            'preferred_username' => 'anderson.contreira',
            'exp' => time() + 3600,
            'iat' => time(),
            'scope' => ['app.read']
        ];
        return $payload;
    }

    /**
     * @return \Firebase\JWT\Key[]
     */
    public static function getParseKeySet(): array
    {
        $jwks = static::getJwtKeys();
        return JWK::parseKeySet(['keys' => $jwks['keys']]);
    }

    public static function getFakeTokenAsObject()
    {
        return json_decode(json_encode(static::getFakeTokenPayload()));
    }
}