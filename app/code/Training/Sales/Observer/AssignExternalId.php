<?php
declare(strict_types=1);

namespace VConnect\Erp\Observer;

use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AssignExternalId implements ObserverInterface
{
    public function __construct(
        private CustomerExtensionFactory $customerExtensionFactory
    ) {}

    public function execute(Observer $observer)
    {
        $customer = $observer->getData('customer');
        $request = $observer->getData('request');
        $customerRequest = $request->getParam('customer');

        $extensionAttributes = $customer->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->customerExtensionFactory->create();
        }

        $extensionAttributes->setExternalId($customerRequest['external_id']);
        $customer->setExtensionAttributes($extensionAttributes);
    }
}
