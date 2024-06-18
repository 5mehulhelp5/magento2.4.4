<?php
declare(strict_types=1);

namespace VConnect\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject\IdentityInterface;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Model\Post;
use VConnect\Blog\Service\PostsProvider;
use Magento\Framework\UrlInterface;

class PostsList extends Template implements IdentityInterface
{
    /** @var PostInterface[]  */
    private array $posts;

    /**
     * PostsList constructor.
     * @param \VConnect\Blog\Service\PostsProvider $postsProvider
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        private PostsProvider $postsProvider,
        private UrlInterface $urlBuilder,
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
     * @param \VConnect\Blog\Model\Post $post
     * @return string
     */
    public function getPostUrl(Post $post): string
    {
        $postUrlKey = $post->getUrlKey();
        if (!empty($postUrlKey)) {
            $postUrl = $this->urlBuilder->getBaseUrl() . 'blog/' . $postUrlKey;
        } else {
            $postUrl = $this->urlBuilder->getUrl(
                'vconnect_blog/post/view',
                ['id' => $post->getData('entity_id'), '_secure' => true]
            );
        }

        return $postUrl;
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
