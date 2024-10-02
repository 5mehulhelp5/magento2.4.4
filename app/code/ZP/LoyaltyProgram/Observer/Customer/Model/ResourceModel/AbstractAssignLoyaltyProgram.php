<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel;

use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Configs\Program\Scope\Config as ProgramScopeConfig;
use Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use ZP\LoyaltyProgram\Model\Validators\Data\Validator;

abstract class AbstractAssignLoyaltyProgram
{
    public function __construct(
        protected LoyaltyProgramManagementInterface $loyaltyProgramManagement,
        protected StoreManagerInterface $storeManager,
        protected ProgramScopeConfig $programScopeConfig,
        protected CustomerExtensionInterfaceFactory $customerExtensionFactory,
        protected LoyaltyProgramRepositoryInterface $loyaltyProgramRepository,
        protected Validator $dataValidator
    ) {}

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->programScopeConfig->isEnabled((int)$this->storeManager->getWebsite()->getId())) {
            $customer = $this->getCustomer($observer);
            /** @var Customer $customer */
            $customerId = $customer->getId();
            if ($customerId !== null) {
                if (!$this->dataValidator->isDataInteger($customerId)) {
                    throw new \Exception('Wrong data type of customer_id!');
                }
                $customerExtension = $customer->getExtensionAttributes();
                $customerExtension = $customerExtension ?: $this->customerExtensionFactory->create();
                $customerProgramId = $customerExtension->getLoyaltyProgramId();
                if ($customerProgramId !== null) {
                    if (!$this->dataValidator->isDataInteger($customerProgramId)) {
                        throw new \Exception('Wrong data type of customer extension attribute `loyalty_program_id`!');
                    }

                    $customerProgram = null;
                    if ($customerProgramId) {
                        try {
                            $customerProgram = $this->loyaltyProgramRepository->get((int)$customerProgramId);
                        } catch (NoSuchEntityException $exception) {
                            //do nothing
                        }

                    }

                    if (!$customerProgram || !$customerProgram->getIsActive()) {
                        $this->loyaltyProgramManagement->assignLoyaltyProgram($customer);
                    }
                }
            }
        }
    }

    abstract protected function getCustomer(Observer $observer): Customer;
}
