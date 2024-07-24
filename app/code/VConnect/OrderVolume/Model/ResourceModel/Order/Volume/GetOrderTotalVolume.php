<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\ResourceModel\Order\Volume;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;

class GetOrderTotalVolume
{
    public const ORDER_VOLUME = 'order_volume';

    public function __construct(private ResourceConnection $resourceConnection)
    {}

    /**
     * @param OrderInterface $order
     * @return float|null
     */
    public function execute(OrderInterface $order): float|null
    {
        $connection = $this->resourceConnection->getConnection();
        $orderId = (int)$order->getEntityId();
        $select = $connection->select()
            ->from('sales_order')
            ->columns(self::ORDER_VOLUME)
            ->where(OrderInterface::ENTITY_ID . ' in (?)', $orderId);
        $data = $connection->fetchAssoc($select);

        return isset($data[$orderId]) ? (float)$data[$orderId][self::ORDER_VOLUME] : null;
    }
}
