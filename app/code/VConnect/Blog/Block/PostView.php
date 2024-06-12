<?php
declare(strict_types=1);

namespace VConnect\Blog\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Model\Post;

class PostView extends Template implements IdentityInterface
{
    /**
     * @var Post
     */
    private Post $post;

    /**
     * PostView constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \VConnect\Blog\Api\PostRepositoryInterface $postRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        private RequestInterface $request,
        private PostRepositoryInterface $postRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return \VConnect\Blog\Model\Post
     */
    public function getPost(): Post
    {
        $this->post = $this->postRepository->get((int)$this->request->getParam('id'));

        return $this->post;
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return $this->post->getIdentities();
    }
}
