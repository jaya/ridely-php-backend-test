<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RequestIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the Request ID from a common HTTP header (e.g., from a proxy)
        // or generate a new UUID if no ID is present.
        $requestId = $request->header('X-Request-ID') ?: (string) Str::uuid();

        // Store the Request ID on the request object for easy access throughout the application.
        $request->attributes->set('requestId', $requestId);

        // Process the request to get the response.
        $response = $next($request);

        // Set the Request ID as a response header so the client can correlate logs.
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}