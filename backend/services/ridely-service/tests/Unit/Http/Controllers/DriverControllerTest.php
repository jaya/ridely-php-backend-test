<?php

namespace Tests\Unit\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Tests\Unit\UnitTestCase;

class DriverControllerTest extends UnitTestCase
{
    /**
     * Test the index method of the DriverController.
     *
     * @return void
     */
    public function testIndex()
    {
        Log::info(
            sprintf("Testing the method %s with parameters: %s", __METHOD__, json_encode(func_get_args()))
        );

        $response = $this->get('/api/v1/drivers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'label',
            'code',
            'message',
            'data' => [
                '*' => ['id',
                    'name',
                    'car' => [
                        'license_plate',
                        'model',
                        'color',
                    ],
                    'available',
                ]
            ],
        ]);
    }
}