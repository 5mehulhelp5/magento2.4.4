<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\OrderVolume\Model\Order\Item\OrderItemsVolumeCalculator;
use VConnect\OrderVolume\Model\Order\OrderVolumeCalculator;
use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\GetOrderItemVolume as OrderItemConfig;
use VConnect\OrderVolume\Model\ResourceModel\Order\Volume\GetOrderTotalVolume as OrderConfig;

class AddOrderVolumeBeforePlaceOrder implements ObserverInterface
{
    public function __construct(
        private OrderItemsVolumeCalculator $orderItemsVolumeCalculator,
        private OrderVolumeCalculator $orderVolumeCalculator
    ) {}

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();

        $this->orderItemsVolumeCalculator->calculate($order->getItems());
        $orderItemsVolumeData = $this->orderItemsVolumeCalculator->getCalculationResult();
        foreach ($order->getItems() as $itemId => $item) {
            $item->setData(OrderItemConfig::ITEM_VOLUME, $orderItemsVolumeData[$itemId]);
        }

        $this->orderVolumeCalculator->calculate($orderItemsVolumeData);
        $order->setData(OrderConfig::ORDER_VOLUME, $this->orderVolumeCalculator->getCalculationResult());
    }
}
