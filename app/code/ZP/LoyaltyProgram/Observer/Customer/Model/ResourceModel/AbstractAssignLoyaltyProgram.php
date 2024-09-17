<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel;

use Magento\Customer\Model\Data\Customer as CustomerDataModel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Config as LoyaltyProgramScopeConfig;
use Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;

abstract class AbstractAssignLoyaltyProgram
{
    public function __construct(
        private LoyaltyProgramManagementInterface $loyaltyProgramManagement,
        private StoreManagerInterface $storeManager,
        private LoyaltyProgramScopeConfig $programScopeConfig,
        private CustomerExtensionInterfaceFactory $customerExtensionFactory,
        private LoyaltyProgramRepositoryInterface $loyaltyProgramRepository
    ) {}

    public function execute(Observer $observer)
    {
        $webSiteId = (int)$this->storeManager->getWebsite()->getId();
        $isLoyaltyProgramEnable = $this->programScopeConfig->isEnabled($webSiteId);
        if ($isLoyaltyProgramEnable) {
            $customerDataModel = $this->getCustomer($observer);
            /** @var CustomerDataModel $customerDataModel */
            $customerId = $customerDataModel->getId();
            if ($customerId) {
                $customerExtension = $customerDataModel->getExtensionAttributes();
                $customerExtension = $customerExtension ? $customerExtension : $this->customerExtensionFactory->create();
                $customerProgramId = $customerExtension->getLoyaltyProgramId();
                $customerProgram = null;
                if ($customerProgramId) {
                    try {
                        $customerProgram = $this->loyaltyProgramRepository->get((int)$customerProgramId);
                    } catch (NoSuchEntityException $exception) {

                    }

                }

                if (!$customerProgram || !$customerProgram->getIsActive()) {
                    $this->loyaltyProgramManagement->assignLoyaltyProgram($customerDataModel);
                }
            }
        }
    }

    abstract protected function getCustomer(Observer $observer): CustomerDataModel;
}
