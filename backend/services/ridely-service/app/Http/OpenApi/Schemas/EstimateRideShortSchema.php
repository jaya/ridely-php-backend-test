<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstimateRideShort",
 *     type="object",
 *     required={"status"},
 *     @OA\Property(property="distance_km", type="number", example="23"),
 *     @OA\Property(property="duration_min", type="number", example="17"),
 *     @OA\Property(property="price_estimate", type="decimal", example="12.50"),
 *     @OA\Property(
 *     property="status",
 *     type="string",
 *     example="PENDING",
 *     enum={"PENDING", "PROCESSING", "READY", "FAILED"}
 *     )
 * )
 */
class EstimateRideShortSchema
{

}