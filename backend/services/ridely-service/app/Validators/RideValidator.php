<?php

namespace App\Validators;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RideValidator
{
    protected ValidationException $exception;

    public function getException(): ValidationException
    {
        return $this->exception;
    }

    public function validateId(int $driverId): bool
    {
        $result = true;

        $validator = Validator::make([
            'id' => $driverId
        ], $this->rules());

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }

    private function rules()
    {
        return [
            'id' => 'required|numeric',
        ];
    }
}