<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * To run only this:
     * php artisan db:seed --class=DriverSeeder
     */
    public function run(): void
    {
        // Criar 5 motoristas aleatórios
        Driver::factory()->count(5)->create();

        // Criar alguns motoristas específicos (dados fixos)
        Driver::create([
            'name' => 'Carlos Silva',
            'car_license_plate' => 'ABC-1234',
            'car_model' => 'Toyota Corolla',
            'car_color' => 'preto',
            'available' => true,
        ]);

        Driver::create([
            'name' => 'Maria Oliveira',
            'car_license_plate' => 'XYZ-9876',
            'car_model' => 'Honda Civic',
            'car_color' => 'prata',
            'available' => false,
        ]);
    }
}
