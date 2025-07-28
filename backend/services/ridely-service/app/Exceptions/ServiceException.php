<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;

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
} 