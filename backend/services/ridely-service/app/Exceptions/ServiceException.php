<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ServiceException extends ApplicationException
{
    protected $code;
    protected $message;
    protected $label;
    protected $params;

    public static function invalidRequest($message, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::INVALID_REQUEST, Response::HTTP_BAD_REQUEST, $message, $params, $previous);
    }

    public static function invalidRequestParam($message, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::INVALID_REQUEST_PARAM, Response::HTTP_BAD_REQUEST, $message, $params, $previous);
    }

    public static function unableToRequestAuthPublicKey($message, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::UNABLE_TO_REQUEST_AUTH_PUBLIC_KEY, Response::HTTP_INTERNAL_SERVER_ERROR, $message, $params, $previous);
    }

    public static function unableToSaveAuthPublicKeyFile(string $message, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::UNABLE_TO_SAVE_AUTH_PUBLIC_KEY_FILE, Response::HTTP_INTERNAL_SERVER_ERROR, $message, $params, $previous);
    }

    public static function invalidToken(string $message, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::INVALID_TOKEN, Response::HTTP_UNAUTHORIZED, $message, $params, $previous);
    }

    public static function missingBearerToken(): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::MISSING_BEARER_TOKEN, Response::HTTP_UNAUTHORIZED);
    }

    public static function notImplemented(): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::SERVICE_NOT_IMPLEMENTED, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function queryException(ErrorMessagesEnum $enum, array $params, \Throwable $previous = null): ServiceException
    {
        return new ServiceException($enum, Response::HTTP_INTERNAL_SERVER_ERROR, "Query exception", $params, $previous);
    }

    public static function databaseTemporarilyUnavailable($message, Throwable $previous = null): ServiceException
    {
        return new ServiceException(ErrorMessagesEnum::SERVICE_TEMPORARILY_UNAVAILABLE, Response::HTTP_SERVICE_UNAVAILABLE, $message, [], $previous);
    }
} 