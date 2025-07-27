<?php

namespace App\Http\Schemas;

/**
 * @OA\Schema(
 *     schema="Car",
 *     type="object",
 *     required={"license_plate", "model", "color"},
 *     @OA\Property(property="license_plate", type="string", example="ABC1234"),
 *     @OA\Property(property="model", type="string", example="Fiat Uno"),
 *     @OA\Property(property="color", type="string", example="Vermelho")
 * )
 */
class CarSchema{}