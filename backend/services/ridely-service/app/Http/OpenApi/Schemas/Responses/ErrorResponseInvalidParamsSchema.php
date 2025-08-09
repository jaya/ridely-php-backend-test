<?php

namespace App\Http\OpenApi\Schemas\Responses;

/**
 * @OA\Schema(
 *     schema="ErrorResponseInvalidParams",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="label", type="string", example="common.error.invalid_request_param"),
 *     @OA\Property(property="code", type="integer", example=12),
 *     @OA\Property(property="message", type="string", example="Invalid Request Parameter: The selected fields.0 is invalid."),
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
class ErrorResponseInvalidParamsSchema
{

}