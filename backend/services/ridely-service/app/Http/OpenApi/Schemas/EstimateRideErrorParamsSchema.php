<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="EstimateRideErrorParams",
 *     type="object",
 *     description="Parâmetros da requisição que causaram o erro",
 *     additionalProperties=@OA\Schema(type="string"),
 *     example={
 *         "pick_up": "Rua A, 100 - Aracaju",
 *         "drop_offz": "Rua B, 200 - Aracaju"
 *     }
 * )
 */

class EstimateRideErrorParamsSchema
{

}