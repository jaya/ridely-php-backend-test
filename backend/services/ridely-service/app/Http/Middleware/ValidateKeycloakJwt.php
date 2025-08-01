<?php

namespace App\Http\Middleware;

use App\Exceptions\ServiceException;
use App\Http\Helpers\ResponseHelper;
use App\Services\JWTKeysService;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use PHPUnit\Framework\Exception;
use Symfony\Component\HttpFoundation\Response;

class ValidateKeycloakJwt
{
    protected JWTKeysService $jwtKeysService;

    public function __construct(JWTKeysService $jwtKeysService)
    {
        $this->jwtKeysService = $jwtKeysService;
    }


    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::error(ServiceException::missingBearerToken());
        }

        try {

            // Note: Main validation will be on Kong, but to prevent internal cluster calls, I added validation to prevent it

            $publicKeys = $this->jwtKeysService->getPublicKeys();


            $decoded = JWT::decode($token, $publicKeys);

            // extra validations
            if ($decoded->iss !== config('keycloak.issuer')) {
                throw new Exception('Invalid token issuer');
            }

            // Scope validation etc...
            // if (!in_array('app.read', $decoded->scope ?? [])) ...

            // Disponibiliza os claims no request
            $request->attributes->set('jwt', (array) $decoded);
            $request->attributes->set('user', $decoded->preferred_username ?? null);

        } catch (\Throwable $e) {
            return ResponseHelper::error(ServiceException::invalidToken($e->getMessage(), [], $e ));
        }

        return $next($request);
    }
}
