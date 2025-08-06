<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;

class DriverException extends ApplicationException
{
    public static function notFound(): DriverException
    {
        return new self(ErrorMessagesEnum::DRIVER_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }
} 