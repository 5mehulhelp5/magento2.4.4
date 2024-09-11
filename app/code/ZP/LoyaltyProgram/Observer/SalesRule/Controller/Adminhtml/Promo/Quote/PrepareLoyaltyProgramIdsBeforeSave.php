<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\SalesRule\Controller\Adminhtml\Promo\Quote;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule\DataProviderPlugin as LoyaltyProgramConfig;

class PrepareLoyaltyProgramIdsBeforeSave implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $observer->getEvent()->getRule();
        $isLoyaltyRule = (int)$rule->getData(LoyaltyProgramConfig::IS_LOYALTY_RULE);
        if ($isLoyaltyRule) {
            $loyaltyPrograms = $rule->getData(LoyaltyProgramConfig::LOYALTY_PROGRAM_IDS);
            $loyaltyPrograms = $loyaltyPrograms ? implode(',', $loyaltyPrograms) : null;
        } else {
            $loyaltyPrograms = null;
        }
        $rule->setData(LoyaltyProgramConfig::LOYALTY_PROGRAM_IDS, $loyaltyPrograms);
    }
}
