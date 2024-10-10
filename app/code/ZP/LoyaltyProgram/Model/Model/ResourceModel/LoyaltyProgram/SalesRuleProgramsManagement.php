<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram;

use Magento\SalesRule\Model\Rule;
use ZP\LoyaltyProgram\Model\Prepare\Data\DataPreparer;
use ZP\LoyaltyProgram\Plugin\SalesRule\Model\Rule\DataProviderPlugin as SalesRuleLoyaltyProgramsConfig;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;

class SalesRuleProgramsManagement
{
    private RuleCollection $ruleCollection;
    private int $rulesCount = 0;
    private ?int $programIdToDelete = null;

    public function __construct(
        private DataPreparer $prepareData,
        private RuleCollectionFactory $ruleCollectionFactory
    ) {}

    /**
     * @param int $programId
     */
    public function collectRules(int $programId): void
    {
        $this->programIdToDelete = $programId;
        $this->ruleCollection = $this->getRuleCollection();
        $this->rulesCount = $this->ruleCollection->getSize();
    }

    public function deleteProgramFromSalesRules(): void
    {
        /**@var Rule $rule */
        foreach ($this->ruleCollection as $rule) {
            $ruleProgramIds = $this->getRuleProgramIds($rule);
            unset($ruleProgramIds[$this->programIdToDelete]);
            $ruleProgramIds = $ruleProgramIds ? implode(',', $ruleProgramIds) : null;
            $rule->setData(SalesRuleLoyaltyProgramsConfig::LOYALTY_PROGRAM_IDS, $ruleProgramIds);
        }

        $this->ruleCollection->save();
    }

    public function getRulesCount(): int
    {
        return $this->rulesCount;
    }

    /**
     * @return RuleCollection
     */
    private function getRuleCollection(): RuleCollection
    {
        $condition[] = ['like' => '%'.$this->programIdToDelete.'%'];

        return $this->ruleCollectionFactory->create()
            ->addFieldToFilter(
                SalesRuleLoyaltyProgramsConfig::LOYALTY_PROGRAM_IDS,
                $condition
            );
    }

    private function getRuleProgramIds(Rule $rule): array
    {
        $programIds = (array)$rule->getData(SalesRuleLoyaltyProgramsConfig::LOYALTY_PROGRAM_IDS);

        if ($programIds) {
            return $this->prepareData->makeArrayKeysLikeValues(
                $this->prepareData->arrayValuesToInteger(
                    $this->prepareData->explodeArray($programIds)
                )
            );
        }

        return $programIds;
    }
}
