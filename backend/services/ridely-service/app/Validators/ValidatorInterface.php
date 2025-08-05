<?php

namespace App\Validators;

use App\Http\Criteria\ListCriteria;

interface ValidatorInterface
{
    public function criteriaRules(ListCriteria $criteria): array;

    public function rules(): array;

    public function validateCreate($data);

    public function validateRead(ListCriteria $criteria);

    public function validateUpdate($data);


}