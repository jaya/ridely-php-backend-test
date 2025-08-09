<?php

namespace App\Http\Criteria\Driver;

class CreateDriverCriteria
{
    public ?string $name;
    public ?string $licensePlate;
    public ?string $model;
    public ?string $color;
    public ?bool $available;

    public function __construct(array $data = null)
    {
        if (!$data) {
            return;
        }

        $this->name = $data['name'] ?? null;
        $this->available = $data['available'] ?? null;
        if ($data['car']) {
            $this->licensePlate = $data['car']['license_plate'] ?? null;
            $this->model = $data['car']['model'] ?? null;
            $this->color = $data['car']['color'] ?? null;
        }

    }

    public function toArray(): array
    {
        return [
            'name' => $this->name ?? '',
            'car' => [
                'license_plate' => $this->licensePlate ?? '',
                'model' => $this->model ?? '',
                'color' => $this->color ?? '',
            ],
            'available' => $this->available ?? true,
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'car.license_plate' => 'required|string|max:10',
            'car.model' => 'required|string|max:255',
            'car.color' => 'required|string|max:50',
            'available' => 'required|boolean',
        ];
    }

    // Getters and setters (opcional, mas seguindo o padrão)

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): CreateDriverCriteria
    {
        $this->name = $name;
        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): CreateDriverCriteria
    {
        $this->licensePlate = $licensePlate;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): CreateDriverCriteria
    {
        $this->model = $model;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): CreateDriverCriteria
    {
        $this->color = $color;
        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): CreateDriverCriteria
    {
        $this->available = $available;
        return $this;
    }
}