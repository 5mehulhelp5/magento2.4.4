<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Plugin\SalesRule\Model;

use Magento\Customer\Api\CustomerRepositoryInterfaceFactory;
use Magento\Customer\Api\Data\CustomerExtensionInterface;
use Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\SalesRule\Model\Utility;
use Magento\SalesRule\Model\Rule;
use Magento\Quote\Model\Quote\Address;
use Magento\Customer\Model\Data\Customer;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterfaceFactory;
use ZP\LoyaltyProgram\Model\LoyaltyProgramRepository;


class UtilityPlugin
{
    private array $conditionsResult = [
        'web_sites' => false,
        'customer_groups' => false,
        'loyalty_programs' => false
    ];
    private array $ruleData = [
        'web_sites' => [],
        'customer_groups' => [],
        'loyalty_programs' => []
    ];
    private array $customerData = [
        'web_sites' => 0,
        'customer_groups' => 0,
        'loyalty_programs' => 0
    ];
    private array $loyaltyProgramData = [
        'web_sites' => 0,
        'customer_groups' => []
    ];

    public function __construct(
        private CustomerRepositoryInterfaceFactory $customerRepositoryFactory,
        private CustomerExtensionInterfaceFactory $customerExtensionFactory,
        private LoyaltyProgramRepositoryInterfaceFactory $loyaltyProgramRepositoryFactory
    ) {}

    public function afterCanProcessRule(Utility $subject, bool $result, Rule $rule, Address $address): bool
    {
        if ($result) {
            $isLoyaltyRule = (bool)$rule->getData('is_loyalty_rule');
            if ($isLoyaltyRule) {
                $customerId = $address->getCustomerId();
                if (!$customerId) {
                    return $this->returnFalse($rule, $address);
                }


                $customer = $this->getCustomer((int)$customerId);
                $customerLoyaltyProgram = $this->getCustomerProgram($customer);
                if (
                    !$this->setLoyaltyProgramDataProperty($customerLoyaltyProgram) ||
                    !$this->setCustomerDataProperty($customer, $customerLoyaltyProgram->getProgramId()) ||
                    !$this->setRuleDataProperty($rule) ||
                    !$this->checkConditionsResult()
                ) {
                    return $this->returnFalse($rule, $address);
                }

                return $this->returnTrue($rule, $address);
            }
        }

        return $result;
    }

    private function checkGroups(
        int $customerGroupId,
        array $ruleCustomerGroupsIds,
        array $programCustomerGroupIds,
    ): bool {
        if (!in_array($customerGroupId, $ruleCustomerGroupsIds) || !in_array($customerGroupId, $programCustomerGroupIds)) {
            return false;
        }

        if (!array_uintersect($programCustomerGroupIds, $ruleCustomerGroupsIds, "strcasecmp")) {
            return false;
        }

        return true;
    }

    private function checkWebSites(
        int $customerWebSiteId,
        int $programWebSiteId,
        array $ruleWebSiteIds
    ): bool {
        if ($customerWebSiteId !== $programWebSiteId || !in_array($customerWebSiteId, $ruleWebSiteIds)) {
            return false;
        }

       return true;
    }

    private function checkLoyaltyPrograms(int $customerProgramId, array $ruleProgramIds): bool
    {
        return in_array($customerProgramId, $ruleProgramIds);
    }

    private function prepareDataOfIds(array $data): array
    {
        $returnData = [];
        if ($data) {
            foreach ($data as $id) {
                $returnData[(int)$id] = (int)$id;
            }
        }

        return $returnData;
    }

    private function returnTrue(Rule $rule, Address $address): bool
    {
        return $this->returnResult($rule, $address, true);
    }

    private function returnFalse(Rule $rule, Address $address): bool
    {
        return $this->returnResult($rule, $address, false);
    }

    private function returnResult(Rule $rule, Address $address, bool $result): bool
    {
        $rule->setIsValidForAddress($address, $result);
        return $result;
    }

    private function setRuleDataProperty(Rule $rule): bool
    {
        $customerGroupsIds = $this->prepareDataOfIds($rule->getCustomerGroupIds());
        $webSiteIds = $this->prepareDataOfIds($rule->getWebsiteIds());
        $loyaltyProgramIds = $rule->getData('loyalty_program_ids');
        if (!$loyaltyProgramIds || !$webSiteIds || !$customerGroupsIds) {
            return false;
        }

        $loyaltyProgramIds = $this->prepareDataOfIds(explode(',', $loyaltyProgramIds));
        $this->ruleData['web_sites'] = $webSiteIds;
        $this->ruleData['customer_groups'] = $customerGroupsIds;
        $this->ruleData['loyalty_programs'] = $loyaltyProgramIds;

        return true;
    }

    private function setCustomerDataProperty(Customer $customer, ?int $loyaltyProgramId): bool
    {
        $groupId = $customer->getGroupId();
        $webSiteId = $customer->getWebsiteId();
        if (!$groupId || !$webSiteId || !$loyaltyProgramId) {
            return false;
        }

        $this->customerData['web_sites'] = (int)$webSiteId;
        $this->customerData['customer_groups'] = (int)$groupId;
        $this->customerData['loyalty_programs'] = (int)$loyaltyProgramId;
        return true;
    }

    private function setLoyaltyProgramDataProperty(?LoyaltyProgram $loyaltyProgram): bool
    {
        if (!$loyaltyProgram) {
            return false;
        }

        $customerGroupsIds = $loyaltyProgram->getCustomerGroupIds();
        $websiteId = $loyaltyProgram->getWebsiteId();
        if (!$customerGroupsIds || !$websiteId) {
            return false;
        }

        $this->loyaltyProgramData['customer_groups'] = $this->prepareDataOfIds($customerGroupsIds);
        $this->loyaltyProgramData['web_sites'] = (int)$websiteId;
        return true;
    }

    /**
     * @param int $customerId
     * @return Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomer(int $customerId): Customer
    {
        /** @var CustomerRepository $customerRepository */
        $customerRepository = $this->customerRepositoryFactory->create();
        return $customerRepository->getById($customerId);
    }

    private function getCustomerProgram(Customer $customer): ?LoyaltyProgram
    {
        try {
            $extensionAttributes = $customer->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->customerExtensionFactory->create();
            }
            /** @var CustomerExtensionInterface $extensionAttributes */

            $loyaltyProgramId = $extensionAttributes->getLoyaltyProgramId();
            if (!$loyaltyProgramId) {
                return null;
            }

            /** @var LoyaltyProgramRepository $loyaltyProgramRepository */
            $loyaltyProgramRepository = $this->loyaltyProgramRepositoryFactory->create();
            return $loyaltyProgramRepository->get((int)$loyaltyProgramId);
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function checkConditionsResult(): bool
    {
        foreach ($this->conditionsResult as $key => $value) {
            $ruleData = $this->ruleData[$key];
            $customerData = $this->customerData[$key];
            $loyaltyProgramData = $this->loyaltyProgramData[$key] ?? null;
            $this->conditionsResult[$key] = $this->checkConditionsData($key, $ruleData, $customerData, $loyaltyProgramData);
        }

        if (in_array(false, $this->conditionsResult)) {
            return false;
        }

        return true;
    }

    private function checkConditionsData(string $fieldType ,$ruleData, $customerData, $programData = null): bool
    {
        if ($fieldType === 'customer_groups') {
            return $this->checkGroups($customerData, $ruleData, $programData);
        } elseif ($fieldType === 'web_sites') {
            return $this->checkWebSites($customerData, $programData, $ruleData);
        } elseif ($fieldType === 'loyalty_programs') {
            return $this->checkLoyaltyPrograms($customerData, $ruleData);
        } else {
            return false;
        }
    }
}
