<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="RideFull",
 *     type="object",
 *     required={"id", "status", "pick_up", "drop_off"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="requested"),
 *     @OA\Property(property="pick_up", type="string", example="Rua X, 200"),
 *     @OA\Property(property="drop_off", type="string", example="Av. Brasil, 1000"),
 *     @OA\Property(property="driver", ref="#/components/schemas/DriverShort"),
 *     @OA\Property(property="passenger", ref="#/components/schemas/Passenger"),
 *     @OA\Property(property="price", type="number", format="float", example=25.00)
 * )
 */
class RideFullSchema
{

}