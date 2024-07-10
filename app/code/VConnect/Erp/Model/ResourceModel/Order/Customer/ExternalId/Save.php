<?php
declare(strict_types=1);

namespace VConnect\Erp\Model\ResourceModel\Order\Customer\ExternalId;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\Erp\Api\Data\ExternalIdInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        private CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * @param OrderInterface $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(OrderInterface $order): void
    {
        $orderId = $this->getOrderId($order);
        $customerExternalId = $this->getCustomerExternalId($order);
        $connection = $this->resourceConnection->getConnection();
        $connection->insert(
            ExternalIdInterface::ORDER_EXTERNAL_ID_TABLE,
            [
                ExternalIdInterface::ORDER_ID => $orderId,
                ExternalIdInterface::CUSTOMER_EXTERNAL_ID => $customerExternalId
            ]
        );
    }

    /**
     * @param OrderInterface $order
     * @return int
     */
    private function getOrderId(OrderInterface $order): int
    {
        return (int)$order->getEntityId();
    }

    /**
     * @param OrderInterface $order
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerExternalId(OrderInterface $order): ?string
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->getById((int)$order->getCustomerId());

        /** @var \Magento\Framework\Api\AttributeInterface|null $customAttributeInterface */
        $customAttributeInterface = $customer->getCustomAttribute(ExternalIdInterface::EXTERNAL_ID);

        return $customAttributeInterface !== null ? $customAttributeInterface->getValue() : null;
    }
}
