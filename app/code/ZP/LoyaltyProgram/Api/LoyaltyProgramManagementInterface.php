<?php

namespace ZP\LoyaltyProgram\Api;

use Magento\Quote\Model\Quote;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use Magento\Customer\Api\Data\CustomerInterface;

interface LoyaltyProgramManagementInterface
{
    /**
     * @param CustomerInterface $customer
     * @return LoyaltyProgramInterface|null
     */
    public function assignLoyaltyProgram(CustomerInterface $customer): ?LoyaltyProgramInterface;

    /**
     * @param CustomerInterface|Quote $entity
     * @param int $webSiteId
     * @param int $customerGroupId
     * @return bool
     */
    public function checkProgramConditions(
        CustomerInterface|Quote $entity,
        int $webSiteId,
        int $customerGroupId
    ): bool;

    /**
     * @return LoyaltyProgramInterface|null
     */
    public function getCustomerProgram(): ?LoyaltyProgramInterface;
}
