<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Volume;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\OrderVolume\Model\Order\Config;

class GetOrderTotalVolume
{
    public function __construct(private ResourceConnection $resourceConnection)
    {}

    /**
     * @param OrderInterface $order
     * @return int|float|null
     */
    public function execute(OrderInterface $order): int|float|null
    {
        $connection = $this->resourceConnection->getConnection();
        $orderId = (int)$order->getEntityId();
        $select = $connection->select()
            ->from(Config::SALES_ORDER_TABLE)
            ->columns(Config::ORDER_VOLUME_COLUMN)
            ->where(Config::ENTITY_ID_COLUMN . ' in (?)', $orderId);
        $data = $connection->fetchAssoc($select);

        return isset($data[$orderId]) ? (float)$data[$orderId][Config::ORDER_VOLUME_COLUMN] : null;
    }
}
