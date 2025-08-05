<?php

namespace App\Validators;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LocationValidator
{
    protected ValidationException $exception;

    public function validate(string $address = null): bool
    {
        $result = true;

        $validator = Validator::make([
            'address' => $address
        ], $this->rules());

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }

    public function rules(): array {
        return [
            'address' => 'required|string',
        ];
    }

    public function getException(): ValidationException
    {
        return $this->exception;
    }
}