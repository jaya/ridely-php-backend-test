<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Driver",
 *     type="object",
 *     required={"id", "name", "car", "available"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Carlos"),
 *     @OA\Property(property="car", ref="#/components/schemas/Car"),
 *     @OA\Property(property="available", type="boolean", example=true),
 *     @OA\Property(
 *           property="_links",
 *           type="object",
 *           @OA\Property(property="self", ref="#/components/schemas/DriverLink"),
 *           @OA\Property(property="update", ref="#/components/schemas/DriverLink"),
 *           @OA\Property(property="replace", ref="#/components/schemas/DriverLink"),
 *           @OA\Property(property="delete", ref="#/components/schemas/DriverLink")
 *       )
 * )
 */
class DriverSchema{}