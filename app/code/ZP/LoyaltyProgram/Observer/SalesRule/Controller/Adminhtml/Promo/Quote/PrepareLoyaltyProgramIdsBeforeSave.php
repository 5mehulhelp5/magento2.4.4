<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\SalesRule\Controller\Adminhtml\Promo\Quote;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule\DataProviderPlugin as LoyaltyProgramConfig;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory as ProgramsCollectionFactory;

class PrepareLoyaltyProgramIdsBeforeSave implements ObserverInterface
{
    public function __construct(private ProgramsCollectionFactory $programsCollectionFactory)
    {}
    public function execute(Observer $observer)
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $observer->getEvent()->getRule();
        $isLoyaltyRule = (int)$rule->getData(LoyaltyProgramConfig::IS_LOYALTY_RULE);
        $loyaltyPrograms = $rule->getData(LoyaltyProgramConfig::LOYALTY_PROGRAM_IDS);
        if ($isLoyaltyRule && $loyaltyPrograms) {
            if (!is_array($loyaltyPrograms)) {
                $loyaltyPrograms = explode(',', (string)$loyaltyPrograms);
            }

            $loyaltyPrograms = $this->programsToString($this->checkLoyaltyPrograms($loyaltyPrograms));
        } else {
            $loyaltyPrograms = null;
        }
        $rule->setData(LoyaltyProgramConfig::LOYALTY_PROGRAM_IDS, $loyaltyPrograms);
    }

    private function checkLoyaltyPrograms(array $rulePrograms): array
    {
        $ruleProgramsIds = [];
        $programsCollection = $this->programsCollectionFactory->create();
        $programsCollection->addFieldToFilter(LoyaltyProgram::PROGRAM_ID, ['in' => $rulePrograms]);
        $programsFromCollection = $programsCollection->getItems();
        if ($programsFromCollection) {
            foreach ($rulePrograms as $programId) {
                $programId = (int)$programId;
                $ruleProgramsIds[$programId] = $programId;
            }

            foreach ($ruleProgramsIds as $programId) {
                if (!array_key_exists($programId, $programsFromCollection)) {
                    unset($ruleProgramsIds[$programId]);
                }
            }
        }

        return $ruleProgramsIds;
    }

    private function programsToString(array $programs): ?string
    {
        return $programs ? implode(',', $programs) : null;
    }
}
