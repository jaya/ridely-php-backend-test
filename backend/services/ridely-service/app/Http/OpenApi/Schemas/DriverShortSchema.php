<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="DriverShort",
 *     type="object",
 *     required={"name", "car"},
 *     @OA\Property(property="name", type="string", example="Carlos"),
 *     @OA\Property(property="car", ref="#/components/schemas/Car"),
 *     @OA\Property(property="available", type="boolean", example=true)
 * )
 */
class DriverShortSchema
{

}