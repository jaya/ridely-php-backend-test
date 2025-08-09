<?php

namespace App\Http\OpenApi\Schemas\Responses;

/**
 * @OA\Schema(
 *     schema="ErrorResponseUnauthorized",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="label", type="string", example="common.error.invalid_token"),
 *     @OA\Property(property="code", type="integer", example=25),
 *     @OA\Property(property="message", type="string", example="Invalid token: token is expired"),
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
class ErrorResponseUnauthorizedSchema
{

}