<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

// TODO I need to review this one
class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse
    {
        $trace = $e->getTrace();
        Log::error(sprintf("Exception Handler - message: %s", $e->getMessage()), [
            'trace' => array_slice($trace, 0 , 4) ?? []
        ]);

        if ($e instanceof ApplicationException) {
            return ResponseHelper::error($e);
        }

        if ($e instanceof ValidationException) {
            $params = $request->all();
            return ResponseHelper::error(ServiceException::invalidRequestParam($e->getMessage(), $params, $e));
        }

        return ResponseHelper::error(new ApplicationException(ErrorMessagesEnum::INTERNAL_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR, previous: $e));
    }
}
