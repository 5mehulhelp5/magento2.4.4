<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin;

use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\GetOrderItemVolume;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;
use Magento\Sales\Api\Data\OrderItemExtensionInterfaceFactory;

class OrderItemRepositoryPlugin
{
    public function __construct(
        private OrderItemExtensionInterfaceFactory $orderItemExtensionInterfaceFactory,
        private GetOrderItemVolume $getOrderItemVolume
    ) {}

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $orderItem
     * @return OrderItemInterface
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $orderItem): OrderItemInterface
    {
        $this->setOrderItemVolumeExtensionAttribute([$orderItem]);

        return $orderItem;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemSearchResultInterface $orderItemSearchResult
     * @return OrderItemSearchResultInterface
     */
    public function afterGetList(
        OrderItemRepositoryInterface $subject,
        OrderItemSearchResultInterface $orderItemSearchResult
    ): OrderItemSearchResultInterface {
        $this->setOrderItemVolumeExtensionAttribute($orderItemSearchResult->getItems());

        return $orderItemSearchResult;
    }

    /**
     * @param OrderItemInterface[] $orderItems
     */
    private function setOrderItemVolumeExtensionAttribute(array $orderItems): void
    {
        foreach ($orderItems as  $orderItem) {
            /** @var \Magento\Sales\Api\Data\OrderItemExtensionInterface|null $extensionAttributes */
            $extensionAttributes = $orderItem->getExtensionAttributes();

            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderItemExtensionInterfaceFactory->create();
            }

            $orderItemVolume = $this->getOrderItemVolume->execute($orderItem);

            $extensionAttributes->setItemVolume($orderItemVolume);
            $orderItem->setExtensionAttributes($extensionAttributes);
        }
    }
}
