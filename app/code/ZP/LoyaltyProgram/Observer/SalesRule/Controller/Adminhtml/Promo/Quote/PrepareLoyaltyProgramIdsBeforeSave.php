<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\SalesRule\Controller\Adminhtml\Promo\Quote;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\SalesRule\Model\Rule;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\Prepare\Data\DataPreparer;
use ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule\DataProviderPlugin as SalesRuleProgramConfig;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory as ProgramsCollectionFactory;
use ZP\LoyaltyProgram\Model\Validators\Data\Validator;

class PrepareLoyaltyProgramIdsBeforeSave implements ObserverInterface
{
    public function __construct(
        private ProgramsCollectionFactory $programsCollectionFactory,
        private Validator $dataValidator,
        private DataPreparer $prepareData
    ) {}

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $rule = $observer->getEvent()->getRule();
        $isLoyaltyRule = (bool)$rule->getData(SalesRuleProgramConfig::IS_LOYALTY_RULE);
        $loyaltyPrograms = $rule->getData(SalesRuleProgramConfig::LOYALTY_PROGRAM_IDS);
        if ($isLoyaltyRule && $loyaltyPrograms !== null && !empty($loyaltyPrograms)) {
            $loyaltyPrograms = $this->dataValidator->validateMultiselectFieldIntData(
                $loyaltyPrograms, SalesRuleProgramConfig::LOYALTY_PROGRAM_IDS, 'SalesRule'
            );

            if ($loyaltyPrograms) {
                $loyaltyPrograms = $this->prepareData->makeArrayKeysLikeValues(
                    $this->prepareData->arrayValuesToInteger($loyaltyPrograms)
                );

                $loyaltyPrograms = $this->checkLoyaltyPrograms($loyaltyPrograms);
                $loyaltyPrograms = $loyaltyPrograms ? implode(',', $loyaltyPrograms) : null;
            }
        } else {
            $loyaltyPrograms = null;
        }

        $rule->setData(SalesRuleProgramConfig::LOYALTY_PROGRAM_IDS, $loyaltyPrograms);
    }

    private function checkLoyaltyPrograms(array $ruleProgramIds): array
    {
        $programsCollection = $this->programsCollectionFactory->create();
        $programsCollection->addFieldToFilter(LoyaltyProgram::PROGRAM_ID, ['in' => $ruleProgramIds]);

        $validProgramIds = array_map(function($program) {
            return (int)$program->getId();
        }, $programsCollection->getItems());

        return array_values(array_intersect($ruleProgramIds, $validProgramIds));
    }
}
