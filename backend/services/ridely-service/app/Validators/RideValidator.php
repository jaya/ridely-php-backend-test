<?php

namespace App\Validators;

use App\Enums\ErrorMessagesEnum;
use App\Http\Criteria\Ride\CreateRideCriteria;

class RideValidator extends AbstractValidator
{

    protected function setValidFields()
    {
        $this->validFields = [];
    }

    public function validateCreate(CreateRideCriteria $criteria)
    {
        return $this->commonValidator($criteria->toArray(), $criteria->rules(), ErrorMessagesEnum::INVALID_DRIVER_DATA->message());
    }

    protected function rules()
    {
        return [];
    }

}