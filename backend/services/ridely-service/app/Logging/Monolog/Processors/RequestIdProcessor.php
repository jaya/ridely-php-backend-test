<?php

namespace App\Logging\Monolog\Processors;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class RequestIdProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $requestId = request()?->attributes->get('requestId');

        if ($requestId) {
            return $record->with(extra: array_merge($record->extra, [
                'request_id' => $requestId,
            ]));
        }
        return $record;
    }
}