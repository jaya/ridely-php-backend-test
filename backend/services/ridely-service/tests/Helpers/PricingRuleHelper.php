<?php

namespace Tests\Helpers;

use App\Converters\DriverConverter;
use app\Converters\PricingRuleConverter;
use App\Converters\RideConverter;
use App\Enums\RideStatusEnum;
use App\Models\Ride;
use Faker\Factory as Faker;

class PricingRuleHelper
{
    public static function getPricingRuleListSample(): array
    {
        return [
            self::getPricingRuleDefault(),
            self::getPricingRuleFlag1(),
            self::getPricingRuleFlag2()
        ];
    }

    public static function getPricingRuleSample(): array
    {
        $faker = Faker::create();

        return $faker->randomElement(self::getPricingRuleListSample());
    }

    public static function getRideModelListSample(): array
    {
        $newData = [];
        $data = static::getPricingRuleListSample();
        foreach ($data as $ride) {
            $newData[] = PricingRuleConverter::convertFromArrayToModel($ride);
        }

        return $newData;
    }

    public static function getPricingRuleFlag2()
    {
        return [
            'name' => 'flag_2',
            'base_fare' => 6.00,
            'price_per_km' => 3.50,
            'is_rush_hour' => false,
            'is_flag_2' => true,
        ];
    }

    public static function getPricingRuleFlag1()
    {
        return [
            'name' => 'flag_1',
            'base_fare' => 5.00,
            'price_per_km' => 3.00,
            'is_rush_hour' => true,
            'is_flag_2' => false,
        ];
    }

    public static function getPricingRuleDefault()
    {
        return [
            'name' => 'default',
            'base_fare' => 4.00,
            'price_per_km' => 2.50,
            'is_rush_hour' => false,
            'is_flag_2' => false,
        ];
    }


}