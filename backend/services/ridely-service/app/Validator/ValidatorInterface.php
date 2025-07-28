<?php

namespace App\Validator;

use App\Http\Criteria\Criteria;

interface ValidatorInterface
{
    public function criteriaRules(Criteria $criteria): array;

    public function rules(): array;

    public function validateCreate($data);

    public function validateRead(Criteria $criteria);

    public function validateUpdate($data);


}