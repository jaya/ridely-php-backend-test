<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\EstimateService;
use Illuminate\Support\Facades\Log;

class EstimateServiceTest extends TestCase
{
    public function test_calculate_returns_correct_value_and_logs()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Estimate calculated ', \Mockery::on(function ($data) {
                return isset($data['id'], $data['price']) && is_numeric($data['price']);
            }));

        $service = new EstimateService();

        $price = $service->calculate(10, 20);

        $expected = 5.00 + (10 * 2.50) + (20 * 0.50);
        $this->assertEquals($expected, $price);
    }
}
