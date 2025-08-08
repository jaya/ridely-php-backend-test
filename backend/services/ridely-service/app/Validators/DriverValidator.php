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

class DriverValidator implements ValidatorInterface
{
    protected array $validFields = [];

    protected ValidationException $exception;

    public function __construct()
    {
        $this->validFields = Driver::$fields;
    }

    public function validateCreate(CreateDriverCriteria $criteria): bool
    {
        return $this->commonValidator($criteria->toArray(), $criteria->rules());
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
        $rules = array_merge($this->rules(), [
            // Note: this will make the validator check the db
            //'id' => 'required|numeric|exists:drivers,id',
            'id' => 'required|numeric',
        ]);
        return $this->commonValidator($data, $rules);
    }

    public function validateDelete($id): bool
    {
        $rules = [
            'id' => 'required|numeric',
        ];
        return $this->commonValidator(['id' => $id], $rules);
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
    public function commonValidator($data, $rules): bool
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

    /**
     * TODO remover
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

    /**
     * @param ListCriteria $criteria
     * @return array|\Illuminate\Validation\Rules\In[][]|\string[][]
     */
    public function appendDatabaseFields(ListCriteria $criteria): array
    {
        return $criteria->rules($this->validFields);
    }
}