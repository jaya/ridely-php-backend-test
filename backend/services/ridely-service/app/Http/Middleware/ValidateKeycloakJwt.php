<?php

namespace App\Http\Middleware;

use App\Enums\ErrorMessagesEnum;
use App\Exceptions\ServiceException;
use App\Http\Helpers\ResponseHelper;
use App\Services\JWTKeysService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $devMode = env('APP_DEV_MODE', false);
        if ($devMode) {
            return $next($request);
        }

        $token = $request->bearerToken();

        if (!$token) {
            Log::error(ErrorMessagesEnum::MISSING_BEARER_TOKEN->message(), ['token' => $token]);
            return ResponseHelper::error(ServiceException::missingBearerToken());
        }

        try {

            // Note: Main validation will be on Kong, but to prevent internal cluster calls, I added validation to prevent it

            $publicKeys = $this->jwtKeysService->getPublicKeys();
            if (!isset($publicKeys)) {
                Log::error("Unable to get public keys");
            }

            $decoded = $this->jwtKeysService->decodeToken($token, $publicKeys);


            // extra validations
            if ($decoded->iss !== config('keycloak.issuer')) {
                Log::error(ErrorMessagesEnum::INVALID_TOKEN->message(),
                    [
                        'token' => $token,
                        'iss' => $decoded->iss,
                        'expected_iss' => config('keycloak.issuer'),
                    ]
                );
                throw new Exception(sprintf('Invalid token issuer: %s', $decoded->iss));
            }




            // Scope validation etc...
            // if (!in_array('app.read', $decoded->scope ?? [])) ...

            // Disponibiliza os claims no request
            $request->attributes->set('jwt', (array) $decoded);
            try {
                $userData = null;

                if (isset($decoded->preferred_username)) {
                    $userData = [
                        'id' => $decoded->id ?? $decoded->sid,
                        'preferred_username' => $decoded->preferred_username,
                    ];
                }

                $request->attributes->set('user', $userData);

            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
                throw $ex;
            }


        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            Log::error(ErrorMessagesEnum::INVALID_TOKEN->message($e->getMessage()), ['token' => $token]);
            return ResponseHelper::error(ServiceException::invalidToken($e->getMessage(), [], $e ));
        }

        return $next($request);
    }


}
