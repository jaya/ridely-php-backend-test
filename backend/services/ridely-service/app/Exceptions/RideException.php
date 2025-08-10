<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Symfony\Component\HttpFoundation\Response;

class RideException extends ApplicationException
{

    public static function notFound(): RideException
    {
        return new self(ErrorMessagesEnum::RIDE_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }

    public static function rideEstimateNotFound(): RideException
    {
        return new self(ErrorMessagesEnum::RIDE_ESTIMATE_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }

    public static function invalidState($message): RideException
    {
        return new self(ErrorMessagesEnum::RIDE_INVALID_STATE, Response::HTTP_BAD_REQUEST, $message);
    }

    public static function noDriversAvailable(): RideException
    {
        return new self(ErrorMessagesEnum::RIDE_NO_DRIVERS_AVAILABLE, Response::HTTP_NOT_ACCEPTABLE);
    }

    public static function unableToLocateAddressData(): RideException
    {
        return new self(ErrorMessagesEnum::RIDE_UNABLE_TO_LOCATE_ADDRESS_DATA, Response::HTTP_BAD_REQUEST);
    }

    public static function pricingRuleNotFound(): RideException
    {
        //throw new \Exception("Pricing rule not found");
        return new self(ErrorMessagesEnum::RIDE_PRICING_RULE_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function estimateNotFound()
    {
        return new self(ErrorMessagesEnum::RIDE_ESTIMATE_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function rideWithoutDriver()
    {
        return new self(ErrorMessagesEnum::INVALID_RIDE_DATA, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}