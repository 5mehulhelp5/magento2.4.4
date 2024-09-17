<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Plugin\SalesRule\Model;

use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Validator;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Model\Config as ProgramScopeConfig;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule\DataProviderPlugin as RuleConfig;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory as ProgramsCollectionFactory;
use Magento\SalesRule\Model\ResourceModel\Rule as RuleResource;

class ValidatorPlugin
{
    private bool $ruleNeedToUpdate;

    public function __construct(
        private StoreManagerInterface $storeManager,
        private ProgramScopeConfig $programScopeConfig,
        private ProgramsCollectionFactory $programsCollectionFactory,
        private RuleResource $ruleResource
    ) {}

    public function afterGetRules(Validator $subject, RuleCollection $ruleCollection)
    {
       if ($ruleCollection->getItems()) {
           if(!$this->programScopeConfig->isEnabled($this->storeManager->getWebsite()->getId())) {
               /**
                * @var int $ruleId
                * @var Rule $rule
                */
               foreach ($ruleCollection as $ruleId => $rule) {
                   if ($rule->getData(RuleConfig::IS_LOYALTY_RULE)) {
                       $ruleCollection->removeItemByKey($ruleId);
                   }
               }
           } else {
               /** @var Rule $rule */
               foreach ($ruleCollection as $rule) {
                   $this->ruleNeedToUpdate = false;
                   $ruleProgramIds = $rule->getData(RuleConfig::LOYALTY_PROGRAM_IDS);
                   if ($ruleProgramIds) {
                       if (!is_array($ruleProgramIds)) {
                           $ruleProgramIds = explode(',', (string)$ruleProgramIds);
                       }

                       $ruleProgramIds = $this->checkLoyaltyPrograms($ruleProgramIds);
                       if ($this->ruleNeedToUpdate) {
                           $rule->setData(RuleConfig::LOYALTY_PROGRAM_IDS, $this->programsToString($ruleProgramIds));
                           $this->ruleResource->save($rule);
                       }
                   }
               }
           }
       }

        return $ruleCollection;
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

        if (!$programsFromCollection || count($rulePrograms) !== count($ruleProgramsIds)) {
            $this->ruleNeedToUpdate = true;
        }

        return $ruleProgramsIds;
    }

    private function programsToString(array $programs): ?string
    {
        return $programs ? implode(',', $programs) : null;
    }
}
