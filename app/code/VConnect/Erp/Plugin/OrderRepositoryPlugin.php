<?php
declare(strict_types=1);

namespace VConnect\Erp\Plugin;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use VConnect\Erp\Model\ResourceModel\Order\Customer\ExternalId\Get as GetCustomerExternalId;

class OrderRepositoryPlugin
{
    public function __construct(
        private ExtensionAttributesFactory $extensionAttributesFactory,
        private GetCustomerExternalId $getCustomerExternalId
    ) {}

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        /** @var \Magento\Sales\Api\Data\OrderExtensionInterface|null $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderInterface::class);
        }

        $orderExternalId = $this->getCustomerExternalId->execute($order);

        $extensionAttributes->setExternalId($orderExternalId);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
