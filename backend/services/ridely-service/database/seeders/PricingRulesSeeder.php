<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pricing_rules')->insert([
            [
                'name' => 'default',
                'base_fare' => 4.00,
                'price_per_km' => 2.50,
                'is_rush_hour' => false,
                'is_flag_2' => false,
            ],
            [
                'name' => 'flag_1',
                'base_fare' => 5.00,
                'price_per_km' => 3.00,
                'is_rush_hour' => true,
                'is_flag_2' => false,
            ],
            [
                'name' => 'flag_2',
                'base_fare' => 6.00,
                'price_per_km' => 3.50,
                'is_rush_hour' => false,
                'is_flag_2' => true,
            ],
        ]);
    }
}
