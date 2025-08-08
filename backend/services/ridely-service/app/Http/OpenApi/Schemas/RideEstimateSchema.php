<?php


namespace App\Http\OpenApi\Schemas;

/**
 *
 * @OA\Schema(
 *      schema="RideStatus",
 *      type="string",
 *      description="Status da corrida",
 *      enum={"REQUESTED", "ACCEPTED", "FINISHED", "CANCELLED", "REFUSED"},
 *      example="REQUESTED"
 *  )
 *
 * @OA\Schema(
 * schema="RideEstimate",
 * type="object",
 * @OA\Property(property="distance_km", type="number", nullable=true, example=null),
 * @OA\Property(property="duration_min", type="number", nullable=true, example=null),
 * @OA\Property(property="price_estimate", type="number", nullable=true, example=null),
 * @OA\Property(property="status", ref="#/components/schemas/RideStatus")
 * )
 */
class RideEstimateSchema
{

}