<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\GetOrderItemVolume;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;

class OrderItemRepositoryPlugin
{
    public function __construct(
        private ExtensionAttributesFactory $extensionAttributesFactory,
        private GetOrderItemVolume $getOrderItemVolume
    ) {}

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $order
     * @return OrderItemInterface
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $orderItem): OrderItemInterface
    {
        /** @var \Magento\Sales\Api\Data\OrderItemExtensionInterface|null $extensionAttributes */
        $extensionAttributes = $orderItem->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(OrderItemInterface::class);
        }

        $orderItemVolume = $this->getOrderItemVolume->execute($orderItem);

        $extensionAttributes->setItemVolume($orderItemVolume);
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }

    public function afterGetList(
        OrderItemRepositoryInterface $subject,
        OrderItemSearchResultInterface $orderItemSearchResult
    ) {
        foreach ($orderItemSearchResult->getItems() as $orderItem) {
            /** @var \Magento\Sales\Api\Data\OrderItemExtensionInterface|null $extensionAttributes */
            $extensionAttributes = $orderItem->getExtensionAttributes();

            if ($extensionAttributes === null) {
                $extensionAttributes = $this->extensionAttributesFactory->create(OrderItemInterface::class);
            }

            $orderItemVolume = $this->getOrderItemVolume->execute($orderItem);

            $extensionAttributes->setItemVolume($orderItemVolume);
            $orderItem->setExtensionAttributes($extensionAttributes);
        }

        return $orderItemSearchResult;
    }
}
