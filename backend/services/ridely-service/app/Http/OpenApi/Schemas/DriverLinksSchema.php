<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *      schema="DriverListLink",
 *      type="object",
 *      @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers?limit=2&fields=id&page=1"),
 *      @OA\Property(property="rel", type="string", example="get"),
 *      @OA\Property(property="method", type="string", example="GET")
 *  )
 * @OA\Schema(
 *      schema="DriverLink",
 *      type="object",
 *      @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers/1"),
 *      @OA\Property(property="rel", type="string", example="get"),
 *      @OA\Property(property="method", type="string", example="GET")
 *  )
 *
 * @OA\Schema(
 *     schema="DriverLinks",
 *     type="object",
 *     @OA\Property(property="self", ref="#/components/schemas/DriverListLink"),
 *     @OA\Property(property="next", ref="#/components/schemas/DriverListLink"),
 *     @OA\Property(property="previous", ref="#/components/schemas/DriverListLink"),
 *     @OA\Property(property="first", ref="#/components/schemas/DriverListLink"),
 *     @OA\Property(property="last", ref="#/components/schemas/DriverListLink")
 * )
 */
class DriverLinksSchema
{

}