<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *      schema="RootSchema",
 *      required={"app"}
 * )
 */
class RootSchema {
    /**
     * @OA\Property(example="ridely-service:1.0.0")
     * @var string
     */
    public string $app;
}
