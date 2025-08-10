<?php

namespace App\Validators;

use App\Http\Criteria\EstimateRideCriteria;
use App\Models\RideEstimate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RideEstimateValidator extends AbstractValidator
{

    public function setValidFields()
    {
        $this->validFields = RideEstimate::$fields;
    }


    public function validate($id, EstimateRideCriteria $criteria)
    {
        $result = true;

        $data = array_merge([
            'id' => $id,
        ], $criteria->toArray());

        $rules = array_merge($criteria->rules(), $this->idRules());
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->exception = new ValidationException($validator);
            $result = false;
            Log::debug(sprintf("Validation fails: %s", $this->exception->getMessage()));
        }

        return $result;
    }


    protected function rules()
    {
        return [];
    }
}