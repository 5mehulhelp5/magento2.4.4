<?php
declare(strict_types=1);

namespace VConnect\Erp\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use VConnect\Erp\Model\ResourceModel\Order\Customer\ExternalId\Save as SaveCustomerExternalId;

class SaveCustomerExternalIdToOrder implements ObserverInterface
{
    public function __construct(private SaveCustomerExternalId $saveCustomerExternalIdToOrder)
    {}

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $this->saveCustomerExternalIdToOrder->execute($order);
    }
}
