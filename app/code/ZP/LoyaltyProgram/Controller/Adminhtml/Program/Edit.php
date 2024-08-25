<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Framework\App\Action\HttpGetActionInterface;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program as AbstractProgramController;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterfaceFactory;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;

class Edit extends AbstractProgramController implements HttpGetActionInterface
{
    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     *
     * Factory to create Post Model Object
     * @param LoyaltyProgramInterfaceFactory $loyaltyProgramFactory
     */
    public function __construct(
        Context $context,
        protected PageFactory $resultPageFactory,
        private LoyaltyProgramInterfaceFactory $loyaltyProgramFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Edit Blog Post
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam(LoyaltyProgram::PROGRAM_ID);
        /** @var LoyaltyProgram $loyaltyProgram */
        $loyaltyProgram = $this->loyaltyProgramFactory->create();

        if ($id) {
            $loyaltyProgram->load($id);
            if (!$loyaltyProgram->getId()) {
                $this->messageManager->addErrorMessage(__('This Loyalty Program does not exist.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Program') : __('New Program'),
            $id ? __('Edit Program') : __('New Program')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Programs'));
        $resultPage->getConfig()->getTitle()->prepend(
            $loyaltyProgram->getId() ? $loyaltyProgram->getProgramName() : __('New Program')
        );

        return $resultPage;
    }
}
