<?php

namespace App\Validators;

use App\Enums\ErrorMessagesEnum;
use App\Http\Criteria\Driver\CreateDriverCriteria;
use App\Http\Criteria\ListCriteria;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DriverValidator extends AbstractValidator
{


    protected ValidationException $exception;

    public function setValidFields()
    {
        $this->validFields = Driver::$fields;
    }

    public function validateCreate(CreateDriverCriteria $criteria): bool
    {
        return $this->commonValidator($criteria->toArray(), $criteria->rules(), ErrorMessagesEnum::INVALID_DRIVER_DATA->message());
    }

    public function validateRead(ListCriteria $criteria): bool
    {
        $result = true;

        $rules = $this->appendDatabaseFields($criteria);
        $validator = Validator::make($criteria->toArray(), $rules);

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }


    public function validateUpdate($data): bool
    {
        $rules = array_merge($this->rules(), $this->idRules());
        return $this->commonValidator($data, $rules, ErrorMessagesEnum::INVALID_DRIVER_DATA->message());
    }

    public function validateDelete($id): bool
    {
        return $this->commonValidator(['id' => $id], $this->idRules(), ErrorMessagesEnum::INVALID_DRIVER_DATA->message());
    }

    public function getException(): ValidationException
    {
        return $this->exception;
    }


    /**
     * @param $data
     * @param $rules
     * @return bool
     */

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'car' => 'required|array',
            'car.license_plate' => 'required|string|max:10',
            'car.model' => 'required|string|max:50',
            'car.color' => 'required|string|max:30',
            'available' => 'required|boolean',
        ];
    }


}