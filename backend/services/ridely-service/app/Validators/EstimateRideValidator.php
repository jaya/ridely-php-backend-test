<?php

namespace App\Validators;

use App\Http\Criteria\EstimateRideCriteria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EstimateRideValidator
{
    protected ValidationException $exception;

    public function rules(): array
    {
        return [
            'id' => 'required|string',
        ];
    }

    public function validate(EstimateRideCriteria $criteria, ?string $id)
    {
        $result = true;

        $data = array_merge([
            'id' => $id,
        ], $criteria->toArray());

        $rules = array_merge($criteria->rules(), $this->rules());
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }

    public function getException(): ValidationException
    {
        return $this->exception;
    }
}