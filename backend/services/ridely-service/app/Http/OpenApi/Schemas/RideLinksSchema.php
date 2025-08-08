<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *      schema="RideLink",
 *      type="object",
 *      @OA\Property(property="href", type="string", example="http://localhost:8000/api/v1/rides/5386"),
 *      @OA\Property(property="rel", type="string", example="get"),
 *      @OA\Property(property="method", type="string", example="GET")
 *  )
 *
 * @OA\Schema(
 *      schema="RideLinks",
 *      type="object",
 *      @OA\Property(property="self", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="cancel", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="accept", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="refuse", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="finish", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="estimate", ref="#/components/schemas/RideLink"),
 *      @OA\Property(property="getEstimate", ref="#/components/schemas/RideLink")
 *  )
 */
class RideLinksSchema
{

}