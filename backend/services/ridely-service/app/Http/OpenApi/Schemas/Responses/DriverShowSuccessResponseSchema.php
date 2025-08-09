<?php

namespace App\Http\OpenApi\Schemas\Responses;

/**
 *
 *
 * @OA\Schema(
 *     schema="DriverShowSuccessResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="label", type="string", example="success"),
 *     @OA\Property(property="code", type="integer", example=0),
 *     @OA\Property(property="message", type="string", example="Success"),
 *     @OA\Property(property="data", ref="#/components/schemas/Driver")
 * )
 */
class DriverShowSuccessResponseSchema
{

}