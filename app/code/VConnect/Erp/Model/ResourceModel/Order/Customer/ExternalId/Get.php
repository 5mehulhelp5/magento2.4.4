<?php
declare(strict_types=1);

namespace VConnect\Erp\Model\ResourceModel\Order\Customer\ExternalId;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\Erp\Model\Config;

class Get
{
    public function __construct(private ResourceConnection $resourceConnection)
    {}

    /**
     * @param OrderInterface $order
     * @return string|null
     */
    public function execute(OrderInterface $order): ?string
    {
        $connection = $this->resourceConnection->getConnection();
        $orderId = (int)$order->getEntityId();
        $select = $connection->select()
            ->from(Config::ORDER_EXTERNAL_ID_TABLE)
            ->columns(Config::CUSTOMER_EXTERNAL_ID)
            ->where(Config::ORDER_ID . ' in (?)', $orderId);
        $data = $connection->fetchAssoc($select);

        return $data[$orderId][Config::CUSTOMER_EXTERNAL_ID] ?? null;
    }
}
