<?php


namespace Training\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddProduct implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP .'/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info(
            'Observer for event ' . $observer->getDataByPath('event/name') . ' executed. ' .
            "\n" . print_r($observer->debug(), true)
        );
    }
}
