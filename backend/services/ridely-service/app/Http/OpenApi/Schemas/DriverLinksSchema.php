<?php

namespace App\Http\OpenApi\Schemas;

/**
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