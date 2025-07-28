<?php

namespace App\Enums;

enum ErrorMessagesEnum: int
{
    // Common messages 1-10
    case OK = 1;
    case NOK = 2;

    case UNSUPPORTED_MEDIA_TYPE = 5;



    // Request errors 11 - 20
    case INVALID_REQUEST = 11;
    case INVALID_REQUEST_PARAM = 12;

    // Service errors 21 - 30
    case SERVICE_TEMPORARILY_UNAVAILABLE = 21;

    // Driver errors 31 - 40
    case UNABLE_TO_CREATE_DRIVER = 31;
    case UNABLE_TO_UPDATE_DRIVER = 32;
    case UNABLE_TO_DELETE_DRIVER = 33;
    case DRIVER_NOT_FOUND = 34;
    case UNABLE_TO_LIST_DRIVERS = 35;
    case INVALID_DRIVER_DATA = 36;




    public function label(): string
    {
        return match ($this) {
            self::OK => 'common.success',
            self::NOK => 'common.error.nok',
            self::UNSUPPORTED_MEDIA_TYPE => 'common.error.unsupported_media_type_error',
            self::INVALID_REQUEST => 'common.error.invalid_request',
            self::INVALID_REQUEST_PARAM => 'common.error.invalid_request_param',

            self::SERVICE_TEMPORARILY_UNAVAILABLE => 'common.error.service_unavailable',

            self::UNABLE_TO_CREATE_DRIVER =>  'common.error.unable_to_create_driver',
            self::UNABLE_TO_UPDATE_DRIVER => 'common.error.unable_to_update_driver',
            self::UNABLE_TO_DELETE_DRIVER => 'common.error.unable_to_delete_driver',
            self::DRIVER_NOT_FOUND => 'common.error.driver_not_found',
            self::UNABLE_TO_LIST_DRIVERS => 'common.error.unable_to_list_drivers',
            self::INVALID_DRIVER_DATA => 'common.error.invalid_driver_data',


        };
    }

    public function message(...$args): string
    {
        $message = match ($this) {
            self::OK => 'Success',
            self::NOK => 'Error',
            self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported media type: %s, supported types are (%s)',
            self::INVALID_REQUEST => 'Invalid Request: %s',
            self::INVALID_REQUEST_PARAM => 'Invalid Request Parameter: %s',

            self::SERVICE_TEMPORARILY_UNAVAILABLE => 'Service temporarily unavailable',

            self::UNABLE_TO_CREATE_DRIVER => 'Unable to create driver.',
            self::UNABLE_TO_UPDATE_DRIVER => 'Unable to update driver.',
            self::UNABLE_TO_DELETE_DRIVER => 'Unable to delete driver.',
            self::DRIVER_NOT_FOUND => 'Driver not found.',
            self::UNABLE_TO_LIST_DRIVERS => 'Unable to list drivers.',
            self::INVALID_DRIVER_DATA => 'Invalid Driver Data.',
        };

        if ($args) {
            return vsprintf($message, $args);
        }
        return $message;
    }


}
