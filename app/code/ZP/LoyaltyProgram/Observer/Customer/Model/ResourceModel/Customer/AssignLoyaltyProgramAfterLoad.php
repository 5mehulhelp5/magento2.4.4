<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Data\Customer as CustomerDataModel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel\AbstractAssignLoyaltyProgram;

class AssignLoyaltyProgramAfterLoad extends AbstractAssignLoyaltyProgram implements ObserverInterface
{
    protected function getCustomer(Observer $observer): CustomerDataModel
    {
        $customer = $observer->getEvent()->getCustomer();
        if ($customer && $customer instanceof Customer) {
            $customerDataModel = $customer->getDataModel();
        } else {
            $customerDataModel = $customer;
        }

        return $customerDataModel;
    }
}
