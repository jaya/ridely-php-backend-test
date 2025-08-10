<?php

namespace app\Converters;

use App\Models\PricingRule;

class PricingRuleConverter
{

    public static function convertFromArrayToModel(array $data)
    {
        $pricingRule = new PricingRule();
        $pricingRule->id = $data['id'] ?? null;
        $pricingRule->name = $data['name'] ?? null;
        $pricingRule->base_fare = $data['base_fare'] ?? null;
        $pricingRule->price_per_km = $data['price_per_km'] ?? null;
        $pricingRule->is_rush_hour = $data['is_rush_hour'] ?? null;
        $pricingRule->is_flag_2 = $data['is_flag_2'] ?? null;


        return $pricingRule;
    }
}