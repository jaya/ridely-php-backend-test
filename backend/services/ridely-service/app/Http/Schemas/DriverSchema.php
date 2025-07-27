<?php

namespace App\Http\Schemas;

/**
 * @OA\Schema(
 *     schema="Driver",
 *     type="object",
 *     required={"id", "name", "car", "available"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Carlos"),
 *     @OA\Property(property="car", ref="#/components/schemas/Car"),
 *     @OA\Property(property="available", type="boolean", example=true)
 * )
 */
class DriverSchema{}