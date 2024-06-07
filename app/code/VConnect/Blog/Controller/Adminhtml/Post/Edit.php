<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use VConnect\Blog\Controller\Adminhtml\Post;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use VConnect\Blog\Api\Data\PostInterfaceFactory;

/**
 * Edit Blog Post action.
 */
class Edit extends Post implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Factory to create Post Model Object
     * * @var \VConnect\Blog\Api\Data\PostInterfaceFactory
     */
    private PostInterfaceFactory $postInterfaceFactory;


    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PostInterfaceFactory $postInterfaceFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostInterfaceFactory $postInterfaceFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->postInterfaceFactory = $postInterfaceFactory;
        parent::__construct($context);
    }

    /**
     * Edit Blog Post
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('entity_id');
        $postModel = $this->postInterfaceFactory->create();

        if ($id) {
            $postModel->load($id);
            if (!$postModel->getId()) {
                $this->messageManager->addErrorMessage(__('This blog post no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Post') : __('New Post'),
            $id ? __('Edit Post') : __('New Post')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));
        $resultPage->getConfig()->getTitle()->prepend(
            $postModel->getId() ? $postModel->getTitle() : __('New Post')
        );

        return $resultPage;
    }
}
