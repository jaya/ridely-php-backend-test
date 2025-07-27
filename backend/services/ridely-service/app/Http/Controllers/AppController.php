<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi as OA;

/**
 * @OA\Info(
 *     title="Ridely Service",
 *     version="1.0",
 *     description="Provide Taxi services"
 * )
 */
class AppController extends Controller
{
    /**
     * @OA\Get(
     *     path="/",
     *     summary="Root endpoint",
     *     tags={"Public"},
     *     @OA\Response(
     *          response="200",
     *          description="Success response",
     *          @OA\JsonContent(ref="#/components/schemas/RootSchema")
     *     )
     * )
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $APP_NAME = env('APP_NAME', 'ridely-service');
        $APP_VERSION = env('APP_VERSION', '1.0.0');
        return response()->json(['app' => "${APP_NAME}:${APP_VERSION}"]);
    }

    /**
     * @OA\Get(
     *     path="/health",
     *     summary="Health check endpoint",
     *     tags={"Public"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function alive()
    {
        return response()->json(['status' => 'ok']);
    }

}