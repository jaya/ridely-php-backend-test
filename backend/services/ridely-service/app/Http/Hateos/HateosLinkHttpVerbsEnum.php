<?php

namespace App\Http\Hateos;

enum HateosLinkHttpVerbsEnum
{
    case DELETE;
    case UPDATE;

    case PATCH;

    case GET;

    public function method(): string
    {
        return match ($this) {
            self::DELETE => 'DELETE',
            self::UPDATE => 'UPDATE',
            self::PATCH => 'PATCH',
            self::GET => 'GET',
        };
    }

    public function rel(): string
    {
        return match ($this) {
            self::DELETE => 'delete',
            self::UPDATE => 'replace',
            self::PATCH => 'update',
            self::GET => 'get',
        };
    }
}