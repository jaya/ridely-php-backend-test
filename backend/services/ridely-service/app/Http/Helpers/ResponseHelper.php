<?php

namespace App\Http\Helpers;

use App\Exceptions\ApplicationException;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($data, $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'label' => 'success',
            'code' => 0,
            'message' => 'Success',
            'data' => $data,
        ], $statusCode);
    }

    public static function error(ApplicationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'label' => $exception->getLabel(),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'detail' => config('app.debug') ? $exception->getTrace() : null,
        ], $exception->getStatusCode());
    }

}