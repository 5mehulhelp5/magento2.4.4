<?php
declare(strict_types=1);

namespace VConnect\Blog\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use VConnect\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Api\SortOrderBuilder;

class PostsProvider
{
    /**
     * PostsProvider constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private FilterBuilder $filterBuilder,
        private SortOrderBuilder $sortOrderBuilder,
        private PostRepositoryInterface $postRepository
    ) {}

    /**
     * @return \VConnect\Blog\Api\Data\PostInterface[]
     */
    public function getPosts(): array
    {
        /** @var \Magento\Framework\Api\Filter $filter1 */
        $filter1 = $this->filterBuilder
            ->setField('publish')
            ->setValue(1)
            ->setConditionType('eq')
            ->create();

        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder->setField('created_at')
            ->setDirection('DESC')
            ->create();

        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$filter1])
            ->addSortOrder($sortOrder)
            ->create();

        return $this->postRepository->getList($searchCriteria)->getItems();
    }
}
