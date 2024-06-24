<?php
declare(strict_types=1);

namespace VConnect\Blog\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use VConnect\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Api\SortOrderBuilder;

class PostsProvider
{
    /**
     * PostsProvider constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderBuilder $sortOrderBuilder,
        private PostRepositoryInterface $postRepository
    ) {}

    /**
     * @return \VConnect\Blog\Api\Data\PostInterface[]
     */
    public function getPosts(): array
    {
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder->setField('publish_date')
            ->setDirection('DESC')
            ->create();

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('publish', 1)
            ->addSortOrder($sortOrder)
            ->create();

        return $this->postRepository->getList($searchCriteria)->getItems();
    }
}
