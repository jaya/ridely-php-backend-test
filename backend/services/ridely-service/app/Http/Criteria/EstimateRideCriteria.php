<?php

namespace App\Http\Criteria;

use Illuminate\Validation\Rule;

class EstimateRideCriteria
{
    const CITY = 'Aracaju';

    const STATE = 'SE';

    public ?string $pickUp;

    public ?string $dropOff;

    public function __construct($data)
    {
        $this->pickUp = $this->appendCity($data['pick_up'] ?? null);
        $this->dropOff = $this->appendCity($data['drop_off'] ?? null);
    }

    private function appendCity(?string $location): ?string
    {
        if (
            !empty($location) &&
            stripos($location, self::CITY) === false
        ) {
            return sprintf("%s , %s, %s", $location, self::CITY, self::STATE);
        }

        return $location;
    }

    public function toArray(): array
    {
        return [
            'pick_up' => $this->pickUp,
            'drop_off' => $this->dropOff,
        ];
    }

    public function rules(): array
    {
        return [
            'pick_up' => 'required|string',
            'drop_off' => 'required|string',
        ];

    }

    public function getPickUp(): string
    {
        return $this->pickUp;
    }

    public function setPickUp(string $pickUp): EstimateRideCriteria
    {
        $this->pickUp = $pickUp;
        return $this;
    }

    public function getDropOff(): string
    {
        return $this->dropOff;
    }

    public function setDropOff(string $dropOff): EstimateRideCriteria
    {
        $this->dropOff = $dropOff;
        return $this;
    }


}