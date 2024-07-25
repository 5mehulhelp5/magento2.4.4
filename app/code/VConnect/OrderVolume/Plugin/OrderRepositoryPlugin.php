<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin;

use Magento\Sales\Api\Data\OrderExtensionInterface;
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
        $this->setOrderVolumeExtensionAttribute([$order]);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orderSearchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ): OrderSearchResultInterface {
        $this->setOrderVolumeExtensionAttribute($orderSearchResult->getItems());

        return $orderSearchResult;
    }

    /**
     * @param OrderInterface[] $orders
     */
    private function setOrderVolumeExtensionAttribute(array $orders): void
    {
        foreach ($orders as  $order) {
            /** @var OrderExtensionInterface|null $extensionAttributes */
            $extensionAttributes = $order->getExtensionAttributes();

            if ($extensionAttributes === null) {
                $extensionAttributes = $this->orderExtensionInterfaceFactory->create();
            }

            $orderVolume = $this->getOrderVolume->execute($order);

            $extensionAttributes->setOrderVolume($orderVolume);
            $order->setExtensionAttributes($extensionAttributes);
        }
    }
}
