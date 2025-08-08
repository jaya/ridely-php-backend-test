<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *      schema="RideFull",
 *      type="object",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="status", ref="#/components/schemas/RideStatus"),
 *      @OA\Property(property="pick_up", type="string", example="Avenida Beira Mar, 25"),
 *      @OA\Property(property="drop_off", type="string", example="Avenida Euclides Figueiredo, 65"),
 *      @OA\Property(property="driver", ref="#/components/schemas/DriverShort"),
 *      @OA\Property(property="estimate", ref="#/components/schemas/EstimateRideShort"),
 *      @OA\Property(property="passenger", ref="#/components/schemas/Passenger"),
 *      @OA\Property(property="_links", ref="#/components/schemas/RideLinks")
 *  )
 */
class RideFullSchema
{

}