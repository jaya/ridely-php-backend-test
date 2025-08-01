<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 * schema="PaginationMeta",
 * type="object",
 * @OA\Property(property="limit", type="integer", example=2),
 * @OA\Property(property="total", type="integer", example=17),
 * @OA\Property(property="count", type="integer", example=2),
 * @OA\Property(property="previousPage", type="integer", nullable=true, example=null),
 * @OA\Property(property="currentPage", type="integer", example=1),
 * @OA\Property(property="nextPage", type="integer", example=2),
 * @OA\Property(property="lastPage", type="integer", example=9)
 * )
 */
class PaginationMetaSchema
{

}