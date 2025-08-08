<?php

namespace App\Validators;

use App\Http\Criteria\ListCriteria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class AbstractValidator
{
    protected ValidationException $exception;

    protected array $validFields = [];

    public function __construct()
    {
        $this->setValidFields();
    }

    protected function idRules()
    {
        return [
            'id' => 'required|numeric',
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

    public function getException(): ValidationException
    {
        return $this->exception;
    }

    abstract protected function setValidFields();

    abstract protected function rules();

    protected function commonValidator(array $data, array $rules, $errorMessage): bool
    {
        $result = true;

        if (empty($data)) {
            $this->exception = ValidationException::withMessages([$errorMessage]);
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

    public function validateId(int $id): bool
    {
        $result = true;

        $validator = Validator::make([
            'id' => $id
        ], $this->rules());

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }

}