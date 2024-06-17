<?php
declare(strict_types=1);

namespace VConnect\Blog\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use VConnect\Blog\Api\PostRepositoryInterface;

class PostsPublishManager
{
    private bool $publishOperationResult;

    public function __construct(
        private PostRepositoryInterface $postRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
    ) {}

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(): void
    {
        $posts = $this->getNotPublishedPosts();
        if (!empty($posts)) {
            $this->publishPosts($posts);
        }
    }

    /**
     * @return \VConnect\Blog\Api\Data\PostInterface[]
     */
    private function getNotPublishedPosts(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('publish', 0)
            ->addFilter('publish_date', date('Y-m-d H:i:s'), 'lt')
            ->create();

        return $this->postRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function publishPosts(array $posts): void
    {
        /** @var Post $post */
        foreach ($posts as $post) {
            $post->setPublish(true);
            $this->postRepository->save($post);
        }

        $this->setPublishOperationResult(true);
    }

    /**
     * @param bool $status
     */
    private function setPublishOperationResult(bool $status = false): void
    {
        $this->publishOperationResult = $status;
    }

    /**
     * @return bool
     */
    public function getPublishOperationResult(): bool
    {
        return $this->publishOperationResult;
    }
}
