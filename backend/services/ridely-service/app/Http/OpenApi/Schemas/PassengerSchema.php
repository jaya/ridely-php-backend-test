<?php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Passenger",
 *     type="object",
 *     required={"name", "email"},
 *     @OA\Property(property="name", type="string", example="Maria"),
 *     @OA\Property(property="email", type="string", format="email", example="maria@email.com")
 * )
 */
class PassengerSchema
{

}