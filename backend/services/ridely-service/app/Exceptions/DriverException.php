<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;

class DriverException extends ApplicationException
{
    public static function notFound($params = []): DriverException
    {
        return new self(ErrorMessagesEnum::DRIVER_NOT_FOUND, Response::HTTP_NOT_FOUND, params: $params);
    }

    public static function noRidesWaitingToBeAccepted(): DriverException
    {
        return new self(ErrorMessagesEnum::DRIVER_NO_RIDES_TO_BE_ACCEPTED, Response::HTTP_NOT_FOUND);
    }
} 