<?php

namespace App\Exceptions;

use App\Enums\ErrorMessagesEnum;
use Exception;
use Throwable;

class ApplicationException extends Exception implements Throwable
{
    protected $code;
    protected $message;
    protected $label;
    protected $params;

    protected $enum;

    protected ?Throwable $previous;

    protected int $statusCode;

    public function __construct(ErrorMessagesEnum $enum, int $statusCode, string $message = null, array $params = [], Throwable $previous = null) {
        $this->enum = $enum;
        $this->code = $enum->value;
        $this->label = $enum->label();
        $this->message = isset($message) ? $enum->message($message): $enum->message();
        $this->params = $params;
        $this->previous = $previous;
        $this->statusCode = $statusCode;
        parent::__construct($this->message, $this->code, $this->previous);


    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getEnum(): ErrorMessagesEnum
    {
        return $this->enum;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}