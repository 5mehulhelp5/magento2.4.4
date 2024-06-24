<?php
declare(strict_types=1);

namespace VConnect\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\IdentityInterface;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Service\PostsProvider;

class PostsList extends Template implements IdentityInterface
{
    /** @var PostInterface[]  */
    private array $posts;

    /**
     * PostsList constructor.
     * @param \VConnect\Blog\Service\PostsProvider $postsProvider
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        private PostsProvider $postsProvider,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return \VConnect\Blog\Api\Data\PostInterface[]
     */
    public function getPosts(): array
    {
        $this->posts = $this->postsProvider->getPosts();

        return $this->posts;
    }

    /**
     * @return array
     */
    public function getIdentities(): array
    {
        $identities = [];
        foreach ($this->posts as $post) {
            $identities[] = $post->getIdentities();
        }

        $identities = array_merge([], ...$identities);

        return $identities;
    }
}
