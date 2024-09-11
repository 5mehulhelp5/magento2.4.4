<?php

namespace ZP\LoyaltyProgram\Api;

use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use Magento\Customer\Api\Data\CustomerInterface;

interface LoyaltyProgramManagementInterface
{
    /**
     * @param CustomerInterface $customer
     * @return LoyaltyProgramInterface|null
     */
    public function assignLoyaltyProgram(CustomerInterface $customer): ?LoyaltyProgramInterface;
}
