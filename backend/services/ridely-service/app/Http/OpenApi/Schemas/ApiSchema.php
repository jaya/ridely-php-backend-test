<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Server(
 * url="http://localhost:8000",
 * description="Local API Server"
 * )
 * @OA\Components(
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 * )
 */
class ApiSchema
{

}