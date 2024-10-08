<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Model\Post;
use VConnect\Blog\Model\PostFactory;
use Magento\Framework\Exception\LocalizedException;
use VConnect\Blog\Controller\Adminhtml\Post as AbstractPostController;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultInterface;

/**
 * Save Blog Post action.
 */
class Save extends AbstractPostController implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param PostFactory|null $postFactory
     * @param PostRepositoryInterface|null $postRepository
     */
    public function __construct(
        Context $context,
        private ?PostFactory $postFactory = null,
        private ?PostRepositoryInterface $postRepository = null
    ) {
        $this->postFactory = $postFactory ?: ObjectManager::getInstance()->get(PostFactory::class);
        $this->postRepository = $postRepository ?: ObjectManager::getInstance()->get(PostRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data['publish']) && $data['publish'] === 'true') {
                $data['publish'] = Post::PUBLISHED;
            }

            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            }

            /** @var \VConnect\Blog\Model\Post $model */
            $model = $this->postFactory->create();

            $id = $this->getRequest()->getParam('entity_id');
            if ($id && (is_numeric($id) && !is_float($id))) {
                try {
                    $model = $this->postRepository->get((int)$id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->postRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
