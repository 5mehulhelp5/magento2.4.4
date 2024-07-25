<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetOrderItemVolume
{
    public const ITEM_VOLUME = 'item_volume';

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
            ->from('sales_order_item')
            ->columns(self::ITEM_VOLUME)
            ->where(OrderItemInterface::ITEM_ID . ' in (?)', $orderItemId);
        $data = $connection->fetchAssoc($select);

        return isset($data[$orderItemId]) ? (float)$data[$orderItemId][self::ITEM_VOLUME] : null;
    }
}
