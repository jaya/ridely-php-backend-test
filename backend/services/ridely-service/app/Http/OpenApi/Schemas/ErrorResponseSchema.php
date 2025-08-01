<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="label", type="string", example="error"),
 *     @OA\Property(property="code", type="integer", example=1),
 *     @OA\Property(property="message", type="string", example="Erro ao buscar motoristas."),
 *     @OA\Property(
 *         property="detail",
 *         type="array",
 *         description="Stack trace (visível apenas em ambiente de desenvolvimento)",
 *         @OA\Items(type="object")
 *     )
 * )
 */
class ErrorResponseSchema
{

}