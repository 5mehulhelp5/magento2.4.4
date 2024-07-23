<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\OrderVolume\Model\ResourceModel\Order\Volume\SaveOrderTotalVolume;
use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\SaveOrderItemVolume;

class SaveOrderVolume implements ObserverInterface
{
    public function __construct(
        private SaveOrderTotalVolume $saveOrderTotalVolume,
        private SaveOrderItemVolume $saveOrderItemVolume
    ) {}

    public function execute(Observer $observer): void
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();

        $this->saveOrderItemVolume->execute($order);

        $this->saveOrderTotalVolume->execute($order, $this->saveOrderItemVolume->getOrderItemsVolumeCalculator());
    }
}
