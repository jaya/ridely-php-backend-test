<?php

namespace App\Http\Schemas;

/**
 * @OA\Schema(
 *     schema="RideSimple",
 *     type="object",
 *     required={"id", "status", "pick_up", "drop_off"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="requested"),
 *     @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
 *     @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000")
 * )
 */
class RideSimpleSchema
{

}