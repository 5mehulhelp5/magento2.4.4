<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use VConnect\OrderVolume\Model\Order\Item\OrderItemsVolumeCalculator;
use VConnect\OrderVolume\Model\Order\Item\Config as OrderItemConfig;

class SaveOrderItemVolume
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        private OrderItemsVolumeCalculator $orderItemsVolumeCalculator
    ) {}

    /**
     * @return OrderItemsVolumeCalculator
     */
    public function getOrderItemsVolumeCalculator(): OrderItemsVolumeCalculator
    {
        return $this->orderItemsVolumeCalculator;
    }

    /**
     * @param OrderInterface $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(OrderInterface $order): void
    {
        $orderItemsData = $this->createOrderItemsVolumeUpdateData($order);

        $connection = $this->resourceConnection->getConnection();
        foreach ($orderItemsData as $orderItemId => $orderItemData) {
            $connection->update(
                OrderItemConfig::SALES_ORDER_ITEM_TABLE,
                $orderItemData,
                OrderItemConfig::ITEM_ID_COLUMN . ' = ' . $orderItemId
            );
        }
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function createOrderItemsVolumeUpdateData(OrderInterface $order): array
    {
        $orderItemsData = [];
        $this->orderItemsVolumeCalculator->calculate($order->getItems());
        $orderItemsVolumeData = $this->orderItemsVolumeCalculator->getCalculationResult();
        foreach ($order->getItems() as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            $orderItemId = (int)$orderItem->getItemId();
            $orderItemsData[$orderItemId] = [
                OrderItemConfig::ITEM_VOLUME_COLUMN => $orderItemsVolumeData[$orderItemId]
            ];
        }

        return $orderItemsData;
    }
}
