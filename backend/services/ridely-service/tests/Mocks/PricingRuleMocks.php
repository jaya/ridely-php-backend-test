<?php

namespace Tests\Mocks;

use App\Models\PricingRule;
use Tests\Helpers\PricingRuleHelper;

trait PricingRuleMocks
{

    public function mockPricingRule($data = null): PricingRule
    {
        if (!$data) {
            $data = PricingRuleHelper::getPricingRuleSample();
        }

        return $this->createModelMockWithData(PricingRule::class, $data);
    }

    public function mockPricingRuleModelFilterRuleBasedOnTime(PricingRule $pricingRule = null)
    {
        $this->pricingRuleModelMock->shouldReceive('filterRuleBasedOnTime')
            ->withAnyArgs()
            ->once()
            ->andReturn($pricingRule);
    }


}