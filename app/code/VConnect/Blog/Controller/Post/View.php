<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use VConnect\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use VConnect\Blog\Model\Post;

class View implements HttpGetActionInterface
{
    private Post $post;

    public function __construct(
        private PageFactory $pageFactory,
        private RequestInterface $request,
        private PostRepositoryInterface $postRepositoryInterface,
        private ForwardFactory $forwardFactory
    ) {}

    public function execute()
    {
        try {
            $id = $this->request->getParam('id');
            $this->checkPostId($id);
            $this->isPostPublished();

            /** @var Page $page */
            $page = $this->pageFactory->create();
            $page->getConfig()->getTitle()->set(__('VConnect Blog Post'));

            return $page;
        } catch (\Exception) {
            $resultForward = $this->forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward;
        }
    }

    /**
     * @param string $postId
     * @throws \Exception
     */
    private function checkPostId(string $postId)
    {
        if (!is_numeric($postId) || is_float($postId) || (int)$postId < 0) {
            throw new \Exception(
                'Wrong data type of post id! It has to be int or int in quotes. You received:' . " \'$postId\' ."
            );
        }

        $this->post = $this->postRepositoryInterface->get((int)$postId);
    }

    /**
     * @throws \Exception
     */
    private function isPostPublished()
    {
        if ($this->post->getPublish() === false) {
            throw new \Exception(
                'Post is not available yet, because his attribute `publish` is \'false\' !'
            );
        }
    }
}
