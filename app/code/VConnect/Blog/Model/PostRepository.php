<?php
declare(strict_types=1);

namespace VConnect\Blog\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Api\Data\PostSearchResultsInterface;
use VConnect\Blog\Api\Data\PostSearchResultsInterfaceFactory;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Api\Data\PostInterfaceFactory;
use VConnect\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use VConnect\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PostResource $postResource,
        private PostInterfaceFactory $postFactory,
        private PostCollectionFactory $postCollectionFactory,
        private PostSearchResultsInterfaceFactory $postSearchResultsFactory,
        private CollectionProcessorInterface $collectionProcessor
    ) {

    }

    /**
     * @param \VConnect\Blog\Api\Data\PostInterface $post
     * @return \VConnect\Blog\Api\Data\PostInterface
     * @throws CouldNotSaveException
     */
    public function save(PostInterface $post): PostInterface
    {
        try {
            $this->postResource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $post;
    }

    /**
     * @param int $postId
     * @return \VConnect\Blog\Api\Data\PostInterface
     * @throws NoSuchEntityException
     */
    public function get(int $postId): PostInterface
    {
        $post = $this->postFactory->create();
        $this->postResource->load($post, $postId, 'entity_id');
        if (!$post->getId()) {
            throw new NoSuchEntityException(__("Post entity with the \"entity_id\" doesn\'t exist.", $postId));
        }

        return $post;
    }

    /**
     * @param \VConnect\Blog\Api\Data\PostInterface $post
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $post): bool
    {
        try {
            $this->postResource->delete($post);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param int $postId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $postId): bool
    {
        return $this->delete($this->get($postId));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \VConnect\Blog\Api\Data\PostSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface
    {
        $collection = $this->postCollectionFactory->create();

        /** @var PostSearchResultsInterface $searchResult */
        $searchResult = $this->postSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
