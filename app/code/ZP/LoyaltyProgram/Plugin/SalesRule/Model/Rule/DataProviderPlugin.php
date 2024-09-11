<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule;

use Magento\SalesRule\Model\Rule\DataProvider as SalesRuleDataProvider;

class DataProviderPlugin
{
    public const LOYALTY_PROGRAM_IDS = 'loyalty_program_ids';
    public const IS_LOYALTY_RULE = 'is_loyalty_rule';

    public function afterGetData(SalesRuleDataProvider $subject, ?array $loadedData)
    {
        if ($loadedData) {
            $saleRuleId = array_key_first($loadedData);
            $isLoyaltyRule = (int)$loadedData[$saleRuleId][self::IS_LOYALTY_RULE];
            if ($isLoyaltyRule && $loadedData[$saleRuleId][self::LOYALTY_PROGRAM_IDS]) {
                $loyaltyProgramIds = explode(',', $loadedData[$saleRuleId][self::LOYALTY_PROGRAM_IDS]);
                $loadedData[$saleRuleId][self::LOYALTY_PROGRAM_IDS] = [];
                foreach ($loyaltyProgramIds as $loyaltyProgramId) {
                    $loadedData[$saleRuleId][self::LOYALTY_PROGRAM_IDS][] = $loyaltyProgramId;
                }
            }
        }

        return $loadedData;
    }
}
