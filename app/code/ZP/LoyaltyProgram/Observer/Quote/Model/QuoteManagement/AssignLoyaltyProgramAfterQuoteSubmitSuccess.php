<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Quote\Model\QuoteManagement;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Config as LoyaltyProgramScopeConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Data\Customer;

class AssignLoyaltyProgramAfterQuoteSubmitSuccess implements ObserverInterface
{
    public function __construct(
        private LoyaltyProgramScopeConfig $programScopeConfig,
        private CustomerRepositoryInterface $customerRepository,
        private StoreManagerInterface $storeManager,
        private LoyaltyProgramManagementInterface $loyaltyProgramManagement
    ) {}

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $webSiteId = (int)$this->storeManager->getWebsite()->getId();
        $isLoyaltyProgramEnable = $this->programScopeConfig->isEnabled($webSiteId);
        if ($isLoyaltyProgramEnable && !$this->programScopeConfig->isApplySubtotalChangesAfterInvoice($webSiteId)) {
            $customerId = $observer->getEvent()->getOrder()->getCustomerId();
            if (!$customerId) {
                return;
            }

            /** @var Customer $customer */
            $customer = $this->customerRepository->getById((int)$customerId);
            $this->loyaltyProgramManagement->assignLoyaltyProgram($customer);
        }
    }
}
