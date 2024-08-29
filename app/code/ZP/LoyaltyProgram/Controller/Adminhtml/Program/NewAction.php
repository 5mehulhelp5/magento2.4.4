<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Framework\App\Action\HttpGetActionInterface;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program as AbstractProgramController;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;

class NewAction extends AbstractProgramController implements HttpGetActionInterface
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        protected ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Create new Blog Post
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
