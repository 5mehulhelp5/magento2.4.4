<?php


namespace Training\Sales\Observer;


use Magento\Framework\Event\Observer;

class AddProductBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP .'/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Customer tried add product in cart');
    }
}
