<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Volume;

use Magento\Sales\Api\Data\OrderInterface;
use VConnect\OrderVolume\Model\Order\OrderVolumeCalculator;
use Magento\Framework\App\ResourceConnection;
use VConnect\OrderVolume\Model\Order\Config as OrderConfig;
use VConnect\OrderVolume\Model\Order\Item\OrderItemsVolumeCalculator;

class SaveOrderTotalVolume
{
    public function __construct(
        private ResourceConnection $resourceConnection,
        private OrderVolumeCalculator $orderVolumeCalculator
    ){}

    /**
     * @param OrderInterface $order
     * @param OrderItemsVolumeCalculator $orderItemsVolumeCalculator
     */
    public function execute(OrderInterface $order, OrderItemsVolumeCalculator $orderItemsVolumeCalculator): void
    {
        $orderID = (int)$order->getEntityId();
        $orderTotalVolume = $this->getOrderTotalVolume($orderItemsVolumeCalculator);
        $connection = $this->resourceConnection->getConnection();

        $connection->update(
            OrderConfig::SALES_ORDER_TABLE,
            [OrderConfig::ORDER_VOLUME_COLUMN => $orderTotalVolume],
            OrderConfig::ENTITY_ID_COLUMN . ' = ' . $orderID
        );

        $connection->update(
            OrderConfig::SALES_ORDER_GRID_TABLE,
            [OrderConfig::ORDER_VOLUME_COLUMN => $orderTotalVolume],
            OrderConfig::ENTITY_ID_COLUMN . ' = ' . $orderID
        );
    }

    /**
     * @param OrderItemsVolumeCalculator $orderItemsVolumeCalculator
     * @return int|float
     */
    private function getOrderTotalVolume(OrderItemsVolumeCalculator $orderItemsVolumeCalculator): int|float
    {
        $this->orderVolumeCalculator->calculate($orderItemsVolumeCalculator->getCalculationResult());

        return $this->orderVolumeCalculator->getCalculationResult();
    }
}
