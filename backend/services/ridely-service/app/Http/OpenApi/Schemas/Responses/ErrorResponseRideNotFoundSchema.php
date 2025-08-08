<?php

namespace App\Http\OpenApi\Schemas\Responses;

/**
 * @OA\Schema(
 *     schema="ErrorResponseRideNotFound",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="label", type="string", example="common.error.ride_not_found"),
 *     @OA\Property(property="code", type="integer", example=61),
 *     @OA\Property(property="message", type="string", example="Ride not found"),
 *     @OA\Property(
 *         property="params",
 *         type="array",
 *         description="Parametros da requisição",
 *         @OA\Items(type="string", example="id")
 *     ),
 *     @OA\Property(
 *         property="detail",
 *         type="array",
 *         description="Stack trace (visível apenas em ambiente de desenvolvimento)",
 *         @OA\Items(type="object")
 *     )
 * )
 */
class ErrorResponseRideNotFoundSchema
{

}