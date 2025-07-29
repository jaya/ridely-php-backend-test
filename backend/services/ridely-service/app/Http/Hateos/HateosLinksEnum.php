<?php

namespace App\Http\Hateos;

enum HateosLinksEnum: string
{
    case HREF = 'href';
    case NEXT = 'next';
    case PREVIOUS = 'previous';
    case FIRST = 'first';
    case LAST = 'last';
}
