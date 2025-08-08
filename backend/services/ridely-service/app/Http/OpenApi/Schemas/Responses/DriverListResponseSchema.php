<?php
namespace App\Http\OpenApi\Schemas\Responses;

/**
* @OA\Schema(
*     schema="DriverListLink",
*     type="object",
*     @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers?limit=2&fields=id&page=1"),
*     @OA\Property(property="rel", type="string", example="get"),
*     @OA\Property(property="method", type="string", example="GET")
* )
* @OA\Schema(
*     schema="DriverLink",
*     type="object",
*     @OA\Property(property="href", type="string", example="http://127.0.0.1:8000/api/v1/drivers/1"),
*     @OA\Property(property="rel", type="string", example="get"),
*     @OA\Property(property="method", type="string", example="GET")
* )
*
*
*
* @OA\Schema(
*     schema="DriverListResponse",
*     type="object",
*     @OA\Property(property="success", type="boolean", example=true),
*     @OA\Property(property="label", type="string", example="success"),
*     @OA\Property(property="code", type="integer", example=0),
*     @OA\Property(property="message", type="string", example="Success"),
*     @OA\Property(
*         property="data",
*         type="array",
*         @OA\Items(ref="#/components/schemas/Driver")
*     ),
*     @OA\Property(property="_meta", ref="#/components/schemas/PaginationMeta"),
*     @OA\Property(property="_links", ref="#/components/schemas/DriverLinks")
* )
*/
class DriverListResponseSchema
{
}
