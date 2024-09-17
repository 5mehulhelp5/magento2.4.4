<?php


namespace Training\Sales\Controller\Index;

use Magento\Framework\App\ResponseInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        echo 'Hello World';
        exit();
    }
}
