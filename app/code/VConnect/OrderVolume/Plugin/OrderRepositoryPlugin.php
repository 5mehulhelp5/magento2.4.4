<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin;

use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use VConnect\OrderVolume\Model\ResourceModel\Order\Volume\GetOrderTotalVolume as GetOrderVolume;

class OrderRepositoryPlugin
{
    public function __construct(
        private OrderExtensionInterfaceFactory $orderExtensionInterfaceFactory,
        private GetOrderVolume $getOrderVolume
    ) {}

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        /** @var \Magento\Sales\Api\Data\OrderExtensionInterface|null $orderExtensionAttributes */
        $orderExtensionAttributes = $order->getExtensionAttributes();

        if ($orderExtensionAttributes === null) {
            $orderExtensionAttributes = $this->orderExtensionInterfaceFactory->create();
        }

        $orderVolume = $this->getOrderVolume->execute($order);

        $orderExtensionAttributes->setOrderVolume($orderVolume);
        $order->setExtensionAttributes($orderExtensionAttributes);

        return $order;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ): OrderSearchResultInterface {
        foreach ($orderSearchResult->getItems() as $order) {
            /** @var \Magento\Sales\Api\Data\OrderExtensionInterface|null $extensionAttributes */
            $extensionAttributes = $order->getExtensionAttributes();

            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderExtensionInterfaceFactory->create();
            }

            $orderVolume = $this->getOrderVolume->execute($order);

            $extensionAttributes->setOrderVolume($orderVolume);
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $orderSearchResult;
    }
}
