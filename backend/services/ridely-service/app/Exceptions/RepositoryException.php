<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Exception;

class RepositoryException extends ApplicationException
{
//    public function __construct(ErrorMessagesEnum $enum, string $message = null, array $params = [], Throwable $previous = null)
//    {
//        parent::__construct($enum, Response::HTTP_INTERNAL_SERVER_ERROR, $message, $params, $previous);
//    }

    public static function queryException(ErrorMessagesEnum $enum, array $params, \Throwable $previous = null): RepositoryException
    {
        return new RepositoryException($enum, Response::HTTP_INTERNAL_SERVER_ERROR, "Query exception", $params, $previous);
    }

    public static function databaseTemporarilyUnavailable($message, Throwable $previous = null): RepositoryException
    {
        return new RepositoryException(ErrorMessagesEnum::SERVICE_TEMPORARILY_UNAVAILABLE, Response::HTTP_SERVICE_UNAVAILABLE, $message, [], $previous);
    }

    public static function notFound(ErrorMessagesEnum $enum, array $params, Throwable $previous = null)
    {
        return new RepositoryException($enum, Response::HTTP_NOT_FOUND, "Record not found", $params, $previous);
    }
}