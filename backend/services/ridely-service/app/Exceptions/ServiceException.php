<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Exception;

class ServiceException extends Exception
{
    public static function invalidRequest($message)
    {
        return new self(sprintf(ErrorMessagesEnum::INVALID_REQUEST->value, $message));
    }

    public static function invalidRequestParam(string $param)
    {
        return new self(sprintf(ErrorMessagesEnum::INVALID_REQUEST_PARAM->value, $param));
    }
} 