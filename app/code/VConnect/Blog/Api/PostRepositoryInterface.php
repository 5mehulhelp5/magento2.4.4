<?php

namespace VConnect\Blog\Api;

use VConnect\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use VConnect\Blog\Api\Data\PostSearchResultsInterface;

interface PostRepositoryInterface
{
    /**
     * @param \VConnect\Blog\Api\Data\PostInterface $post
     * @return \VConnect\Blog\Api\Data\PostInterface
     * @throws CouldNotSaveException
     */
    public function save(PostInterface $post): PostInterface;

    /**
     * @param int $postId
     * @return \VConnect\Blog\Api\Data\PostInterface
     */
    public function get(int $postId): PostInterface;

    /**
     * @param \VConnect\Blog\Api\Data\PostInterface $post
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $post): bool;

    /**
     * @param int $postId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $postId): bool;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \VConnect\Blog\Api\Data\PostSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface;
}
