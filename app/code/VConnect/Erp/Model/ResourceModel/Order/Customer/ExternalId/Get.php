<?php
declare(strict_types=1);

namespace VConnect\Erp\Model\ResourceModel\Order\Customer\ExternalId;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\Erp\Api\Data\ExternalIdInterface;

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
            ->from(ExternalIdInterface::ORDER_EXTERNAL_ID_TABLE)
            ->columns(ExternalIdInterface::CUSTOMER_EXTERNAL_ID)
            ->where(ExternalIdInterface::ORDER_ID . ' in (?)', $orderId);
        $data = $connection->fetchAssoc($select);

        return $data[$orderId][ExternalIdInterface::CUSTOMER_EXTERNAL_ID] ?? null;
    }
}
