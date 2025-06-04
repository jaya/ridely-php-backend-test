<?php

namespace App\Http\Controllers;

use App\Events\EstimateRequested;
use App\Http\Requests\EstimateRequest;
use App\Http\Resources\EstimateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateController extends Controller
{
    public function calculate(EstimateRequest $request): JsonResource
    {
        event(new EstimateRequested(
            $request->user(),
            $request->input('distance_km'),
            $request->input('time_in_minutes')
        ));

        return new EstimateResource(['message' => 'The estimate has been processed.']);
    }
}
