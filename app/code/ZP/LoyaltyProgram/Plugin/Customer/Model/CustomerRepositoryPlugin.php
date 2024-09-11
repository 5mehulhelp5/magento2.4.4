<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Plugin\Customer\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Api\Data\CustomerExtensionInterface;
use Magento\Customer\Api\Data\CustomerExtensionInterfaceFactory;

class CustomerRepositoryPlugin
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        private CustomerExtensionInterfaceFactory $customerExtensionFactory
    )
    {}

    public function afterGetById(CustomerRepositoryInterface $subject, CustomerInterface $customer): CustomerInterface
    {
        return $this->get($subject, $customer);
    }

    public function afterGet(CustomerRepositoryInterface $subject, CustomerInterface $customer): CustomerInterface
    {
        return $this->get($subject, $customer);
    }

    private function get(CustomerRepositoryInterface $subject, CustomerInterface $customer): CustomerInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from('zp_loyalty_program_customer', 'program_id')
            ->where('customer_id = (?)', $customer->getId());
        $result = $connection->fetchOne($select);
        if ($result) {
            $extensionAttributes = $customer->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->customerExtensionFactory->create();
            }

            /** @var CustomerExtensionInterface $extensionAttributes */
            $extensionAttributes->setLoyaltyProgramId((int)$result);
            $customer->setExtensionAttributes($extensionAttributes);
        }

        return $customer;
    }
}
