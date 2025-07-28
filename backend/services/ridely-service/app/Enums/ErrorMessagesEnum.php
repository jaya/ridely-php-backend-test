<?php

namespace App\Enums;

enum ErrorMessagesEnum: string
{
    case UNABLE_TO_CREATE_DRIVER = 'Unable to create driver.';
    case UNABLE_TO_UPDATE_DRIVER = 'Unable to update driver.';
    case UNABLE_TO_DELETE_DRIVER = 'Unable to delete driver.';
    case DRIVER_NOT_FOUND = 'Driver not found.';
    case UNABLE_TO_LIST_DRIVERS = 'Unable to list drivers.';
    case INVALID_DRIVER_DATA = 'Invalid Driver Data.';

    // TODO adicionar testes para validar as mensagens das exceptions, ex:  "message": "Invalid Request: The name field is required.",
    case INVALID_REQUEST = 'Invalid Request: %s';
    case INVALID_REQUEST_PARAM = 'Invalid Request Parameter: %s';
}
