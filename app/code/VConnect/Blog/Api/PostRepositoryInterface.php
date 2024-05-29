<?php

namespace VConnect\Blog\Api;

use VConnect\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

interface PostRepositoryInterface
{
    /**
     * @param PostInterface $post
     * @return PostInterface
     * @throws CouldNotSaveException
     */
    public function save(PostInterface $post);

    /**
     * @param $value
     * @param string $field
     * @return PostInterface
     * @throws NoSuchEntityException
     */
    public function get($value, string $field = PostInterface::ENTITY_ID);

//    /**
//     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
//     * @return \Magento\Cms\Api\Data\PageSearchResultsInterface
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param PostInterface $post
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $post);

    /**
     * @param int $postId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $postId);
}
