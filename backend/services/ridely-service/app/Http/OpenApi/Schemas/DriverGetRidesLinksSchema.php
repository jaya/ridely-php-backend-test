<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *      schema="DriverGetRidesListLink",
 *      type="object",
 *      @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers/1/get-rides?limit=2&fields=id&page=1"),
 *      @OA\Property(property="rel", type="string", example="get"),
 *      @OA\Property(property="method", type="string", example="GET")
 *  )
 * @OA\Schema(
 *      schema="DriverGetRidesLink",
 *      type="object",
 *      @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers/1/get-rides"),
 *      @OA\Property(property="rel", type="string", example="get"),
 *      @OA\Property(property="method", type="string", example="GET")
 *  )
 *
 * @OA\Schema(
 *     schema="DriverGetRidesLinks",
 *     type="object",
 *     @OA\Property(property="self", ref="#/components/schemas/DriverGetRidesListLink"),
 *     @OA\Property(property="next", ref="#/components/schemas/DriverGetRidesListLink"),
 *     @OA\Property(property="previous", ref="#/components/schemas/DriverGetRidesListLink"),
 *     @OA\Property(property="first", ref="#/components/schemas/DriverGetRidesListLink"),
 *     @OA\Property(property="last", ref="#/components/schemas/DriverGetRidesListLink")
 * )
 */
class DriverGetRidesLinksSchema
{

}