<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectApiVersion
{
    public function handle(Request $request, Closure $next)
    {
        // Espera URL como /api/v1/...
        $segments = $request->segments();
        $version = $segments[1] ?? 'v1'; // fallback v1

        app()->instance('api.version', strtolower($version));

        return $next($request);
    }
}