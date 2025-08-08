<?php
namespace App\Http\OpenApi\Schemas\Responses;

/**
* @OA\Schema(
*     schema="RideListResponse",
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
*     @OA\Property(property="_links", ref="#/components/schemas/RideLinks")
* )
*/
class RideListResponseSchema
{
}
