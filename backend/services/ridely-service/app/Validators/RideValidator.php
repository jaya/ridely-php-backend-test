<?php

namespace App\Validators;

use App\Enums\ErrorMessagesEnum;
use App\Http\Criteria\Ride\CreateRideCriteria;
use App\Models\Ride;
use Illuminate\Support\Facades\Log;

class RideValidator extends AbstractValidator
{

    protected function setValidFields()
    {
        $this->validFields = Ride::$fields;
    }

    public function validateCreate(CreateRideCriteria $criteria)
    {
        Log::info("Validating ride creation data: ", $criteria->toArray());
        return $this->commonValidator($criteria->toArray(), $criteria->rules(), ErrorMessagesEnum::INVALID_DRIVER_DATA->message());
    }

    protected function rules()
    {
        return [];
    }

}