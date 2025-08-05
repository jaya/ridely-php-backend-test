<?php

namespace App\Enums;

enum RideStatusEnum: string
{
    case REQUESTED = 'REQUESTED';
    case ACCEPTED = 'ACCEPTED';

    case FINISHED = 'FINISHED';

    case CANCELLED = 'CANCELLED';

    case REFUSED = 'REFUSED';
}
