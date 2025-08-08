<?php

namespace App\Validators;

use App\Enums\ErrorMessagesEnum;
use App\Http\Criteria\Ride\CreateRideCriteria;
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

    public function validateCreate(CreateRideCriteria $criteria)
    {
        return $this->commonValidator($criteria->toArray(), $criteria->rules());
    }

    private function commonValidator(array $data, array $rules): bool
    {
        if (empty($rules)) {
            $rules = $this->rules();
        }

        $result = true;

        if (empty($data)) {
            $this->exception = ValidationException::withMessages([ErrorMessagesEnum::INVALID_DRIVER_DATA->message()]);
            $result = false;
        } else {
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $this->exception = new ValidationException($validator);
                $result = false;
            }
        }

        return $result;
    }
}