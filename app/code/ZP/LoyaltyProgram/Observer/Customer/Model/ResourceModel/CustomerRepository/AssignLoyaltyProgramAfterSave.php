<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel\CustomerRepository;

use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Config as LoyaltyProgramScopeConfig;

class AssignLoyaltyProgramAfterSave implements ObserverInterface
{
    public function __construct(
        private LoyaltyProgramManagementInterface $loyaltyProgramManagement,
        private StoreManagerInterface $storeManager,
        private LoyaltyProgramScopeConfig $programScopeConfig,
    ) {}

    public function execute(Observer $observer)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $webSiteId = (int)$this->storeManager->getWebsite()->getId();
        $isLoyaltyProgramEnable = $this->programScopeConfig->isEnabled($storeId);
        if ($isLoyaltyProgramEnable && !$this->programScopeConfig->isApplySubtotalChangesAfterInvoice($webSiteId)) {
            /** @var Customer $customer */
            $customer = $observer->getData('customer_data_object');
            $this->loyaltyProgramManagement->assignLoyaltyProgram($customer);
        }
    }
}
