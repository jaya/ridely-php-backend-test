<?php

namespace App\Http\OpenApi\Schemas\Responses;

/**
 * @OA\Schema(
 *     schema="ErrorResponseNoRidesWaiting",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="label", type="string", example="common.error.driver_no_rides_to_be_accepted"),
 *     @OA\Property(property="code", type="integer", example=47),
 *     @OA\Property(property="message", type="string", example="Driver has no rides to be accepted."),
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
class ErrorResponseNoRidesWaitingSchema
{

}