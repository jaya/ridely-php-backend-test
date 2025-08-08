<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RequestTimeLogger
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
//        Log::info("Before request: {$request->path()} | Method: {$request->method()} | IP: {$request->ip()} | User-Agent: {$request->header('User-Agent')}");
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        $counter = floor($duration/1000);

        $status = 'OK';
        if ($counter > 5) {
            $status = 'EXTREMELY SLOW';
        } else if ($counter > 3) {
            $status = 'VERY SLOW';
        } else if ($counter > 1) {
            $status = 'SLOW';
        }

        $message = "Request performance: [$status][$counter] Request time: {$duration}ms | Route: {$request->path()} | Status: {$response->status()}";

        if ($counter > 5 || $response->status() >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            Log::error($message);
        } elseif ($counter > 3) {
            Log::warning($message);
        }

//        Log::info("After request: {$request->path()} | Method: {$request->method()} | IP: {$request->ip()} | User-Agent: {$request->header('User-Agent')}");

        return $response;
    }
}