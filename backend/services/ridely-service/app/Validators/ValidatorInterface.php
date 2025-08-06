<?php

namespace App\Validators;

use App\Http\Criteria\ListCriteria;

interface ValidatorInterface
{
    public function appendDatabaseFields(ListCriteria $criteria): array;

}