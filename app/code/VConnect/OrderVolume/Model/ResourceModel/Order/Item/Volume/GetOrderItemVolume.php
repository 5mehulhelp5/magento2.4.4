<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderItemInterface;
use VConnect\OrderVolume\Model\Order\Item\Config;

class GetOrderItemVolume
{
    public function __construct(private ResourceConnection $resourceConnection)
    {}

    /**
     * @param OrderItemInterface $orderItem
     * @return int|float|null
     */
    public function execute(OrderItemInterface $orderItem): int|float|null
    {
        $connection = $this->resourceConnection->getConnection();
        $orderItemId = (int)$orderItem->getItemId();
        $select = $connection->select()
            ->from(Config::SALES_ORDER_ITEM_TABLE)
            ->columns(Config::ITEM_VOLUME_COLUMN)
            ->where(Config::ITEM_ID_COLUMN . ' in (?)', $orderItemId);
        $data = $connection->fetchAssoc($select);

        return isset($data[$orderItemId]) ? (float)$data[$orderItemId][Config::ITEM_VOLUME_COLUMN] : null;
    }
}
