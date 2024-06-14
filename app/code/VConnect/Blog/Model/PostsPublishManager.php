<?php
declare(strict_types=1);

namespace VConnect\Blog\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Tests\NamingConvention\true\bool;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Api\PostRepositoryInterface;

class PostsPublishManager
{
    /** @var PostInterface[] */
    private array $noPublishedPosts = [];

    private bool $postsPublishStatus;

    public function __construct(
        private PostRepositoryInterface $postRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
    ) {}

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function publishPosts(): void
    {
        if ($this->getNotPublishedPosts()) {
            $this->setPublishTrue();
        }
    }

    /**
     * @return bool
     */
    private function getNotPublishedPosts(): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('publish', 0)
            ->addFilter('publish_date', date('Y-m-d H:i:s'), 'lt')
            ->create();

        $this->noPublishedPosts = $this->postRepository->getList($searchCriteria)->getItems();

        if (empty($this->noPublishedPosts)) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function setPublishTrue(): void
    {
        /** @var Post $post */
        foreach ($this->noPublishedPosts as $post) {
            $post->setPublish(true);
            $this->postRepository->save($post);
        }

        $this->setPostsPublishStatus(true);
    }

    /**
     * @param bool $status
     */
    private function setPostsPublishStatus(bool $status = false): void
    {
        $this->postsPublishStatus = $status;
    }

    /**
     * @return bool
     */
    public function getPostsPublishStatus(): bool
    {
        return $this->postsPublishStatus;
    }
}
