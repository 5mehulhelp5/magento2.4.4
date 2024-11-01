<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Quote\Model\QuoteManagement;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Configs\Program\Scope\Config as ProgramScopeConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Data\Customer;
use ZP\LoyaltyProgram\Model\Validators\Data\Validator;

class AssignLoyaltyProgramAfterQuoteSubmitSuccess implements ObserverInterface
{
    public function __construct(
        private ProgramScopeConfig $programScopeConfig,
        private CustomerRepositoryInterface $customerRepository,
        private StoreManagerInterface $storeManager,
        private LoyaltyProgramManagementInterface $loyaltyProgramManagement,
        private Validator $dataValidator
    ) {}

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isLoyaltyProgramEnable = $this->programScopeConfig->isEnabled($websiteId);
        if ($isLoyaltyProgramEnable && !$this->programScopeConfig->isApplySubtotalChangesAfterInvoice($websiteId)) {
            $customerId = $observer->getEvent()->getOrder()->getCustomerId();
            if ($customerId === null || $customerId === false) {
                return;
            } elseif (!$this->dataValidator->isDataInteger($customerId)) {
                throw new \Exception('Wrong data type of `customer_id` from Order entity!');
            }

            /** @var Customer $customer */
            $customer = $this->customerRepository->getById((int)$customerId);
            $this->loyaltyProgramManagement->assignLoyaltyProgram($customer);
        }
    }
}