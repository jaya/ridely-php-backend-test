<?php

namespace App\Http\Criteria\Ride;

class CreateRideCriteria
{
    public ?string $passengerName;
    public ?string $passengerEmail;
    public ?string $pickUp;
    public ?string $dropOff;

    public function __construct(array $data = null)
    {
        if (!$data) {
            return;
        }

        $this->pickUp = $data['pick_up'] ?? null;
        $this->dropOff = $data['drop_off'] ?? null;

        if (isset($data['passenger'])) {
            $this->passengerName = $data['passenger']['name'] ?? null;
            $this->passengerEmail = $data['passenger']['email'] ?? null;
        }
    }

    public function toArray(): array
    {
        return [
            'pick_up' => $this->pickUp ?? '',
            'drop_off' => $this->dropOff ?? '',
            'passenger' => [
                'name' => $this->passengerName ?? '',
                'email' => $this->passengerEmail ?? '',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'pick_up' => 'required|string|max:255',
            'drop_off' => 'required|string|max:255',
            'passenger.name' => 'required|string|max:255',
            'passenger.email' => 'required|email|max:255',
        ];
    }

    // Getters e setters opcionais

    public function getPickUp(): ?string
    {
        return $this->pickUp;
    }

    public function setPickUp(string $pickUp): CreateRideCriteria
    {
        $this->pickUp = $pickUp;
        return $this;
    }

    public function getDropOff(): ?string
    {
        return $this->dropOff;
    }

    public function setDropOff(string $dropOff): CreateRideCriteria
    {
        $this->dropOff = $dropOff;
        return $this;
    }

    public function getPassengerName(): ?string
    {
        return $this->passengerName;
    }

    public function setPassengerName(string $name): CreateRideCriteria
    {
        $this->passengerName = $name;
        return $this;
    }

    public function getPassengerEmail(): ?string
    {
        return $this->passengerEmail;
    }

    public function setPassengerEmail(string $email): CreateRideCriteria
    {
        $this->passengerEmail = $email;
        return $this;
    }
}
