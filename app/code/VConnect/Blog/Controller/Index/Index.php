<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

class Index implements HttpGetActionInterface
{
    /**
     * Index constructor.
     * @param PageFactory $pageFactory
     * @param Context $context
     */
    public function __construct(
        private PageFactory $pageFactory
    ) {
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('VConnect Blog'));

        return $page;
    }
}
