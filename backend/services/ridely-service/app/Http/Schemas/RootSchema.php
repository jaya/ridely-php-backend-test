<?php

namespace App\Http\Schemas;

use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;

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
