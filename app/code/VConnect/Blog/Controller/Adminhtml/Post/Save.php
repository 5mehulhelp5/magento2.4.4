<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller\Adminhtml\Post;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Model\Post;
use VConnect\Blog\Model\PostFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
//use Magento\Framework\Registry;
use VConnect\Blog\Controller\Adminhtml\Post as AbstractPostController;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultInterface;

/**
 * Save CMS block action.
 */
class Save extends AbstractPostController implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @param Context $context
//     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param PostFactory|null $postFactory
     * @param PostRepositoryInterface|null $postRepository
     */
    public function __construct(
        Context $context,
//        Registry $coreRegistry,
//        DataPersistorInterface $dataPersistor,
        PostFactory $postFactory = null,
        PostRepositoryInterface $postRepository = null
    ) {
//        $this->dataPersistor = $dataPersistor;
        $this->postFactory = $postFactory ?: ObjectManager::getInstance()->get(PostFactory::class);
        $this->postRepository = $postRepository ?: ObjectManager::getInstance()->get(PostRepositoryInterface::class);
        parent::__construct($context/*, $coreRegistry*/);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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
//                $this->dataPersistor->clear('cms_block');
//                return $this->processBlockReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

//            $this->dataPersistor->set('cms_block', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

//    /**
//     * Process and set the post return
//     *
//     * @param \VConnect\Blog\Model\Post $model
//     * @param array $data
//     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
//     * @return \Magento\Framework\Controller\ResultInterface
//     */
//    private function processBlockReturn(Post $model, array $data, ResultInterface $resultRedirect): ResultInterface
//    {
//        $redirect = $data['back'] ?? 'close';
//
//        if ($redirect ==='continue') {
//            $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId()]);
//        } elseif ($redirect === 'close') {
//            $resultRedirect->setPath('*/*/');
//        } elseif ($redirect === 'duplicate') {
//            $duplicateModel = $this->postFactory->create(['data' => $data]);
//            $duplicateModel->setId(null);
//            $duplicateModel->setIdentifier($data['identifier'] . '-' . uniqid());
//            $duplicateModel->setIsActive(Post::NOT_PUBLISHED);
//            $this->blockRepository->save($duplicateModel);
//            $id = $duplicateModel->getId();
//            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
//            $this->dataPersistor->set('cms_block', $data);
//            $resultRedirect->setPath('*/*/edit', ['block_id' => $id]);
//        }
//        return $resultRedirect;
//    }
}
