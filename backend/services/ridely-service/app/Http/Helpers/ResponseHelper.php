<?php

namespace App\Http\Helpers;

use App\Exceptions\ApplicationException;
use App\Http\Hateos\HateosMetadata;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success($data, $statusCode = 200, HateosMetadata $metadata = null, bool $hateos = true): JsonResponse
    {


        $body = [
            'success' => true,
            'label' => 'success',
            'code' => 0,
            'message' => 'Success',
            'data' => $data,
        ];


        if ($hateos && $metadata) {
            $body['_meta'] = $metadata->meta()->toArray();
            $body['_links'] = $metadata->links();
        }
        return response()->json($body, $statusCode);
    }

    public static function error(ApplicationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'label' => $exception->getLabel(),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'params' => $exception->getParams(),
            'details' => config('app.debug') ? $exception->getTrace() : null,
        ], $exception->getStatusCode());

    }

}