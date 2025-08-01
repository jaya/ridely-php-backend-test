<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Ride",
 *     type="object",
 *     required={"id", "status", "drop_off", "pick_up", "passenger"},
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="status", type="string", example="waiting"),
 *     @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000"),
 *     @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
 *     @OA\Property(property="passenger", ref="#/components/schemas/Passenger")
 * )
 */
class RideSchema
{

}