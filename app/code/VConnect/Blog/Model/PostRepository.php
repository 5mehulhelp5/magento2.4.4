<?php

namespace VConnect\Blog\Model;

use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Api\Data\PostInterfaceFactory;
use VConnect\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class PostRepository implements PostRepositoryInterface
{
    private PostResource $postResource;
    private PostInterfaceFactory $postFactory;

    public function __construct(PostResource $postResource, PostInterfaceFactory $postFactory)
    {
        $this->postResource = $postResource;
        $this->postFactory = $postFactory;
    }

    /**
     * @param PostInterface $post
     * @return PostInterface
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
     * @param $value
     * @param string $field
     * @return PostInterface
     * @throws NoSuchEntityException
     */
    public function get($value, string $field = PostInterface::ENTITY_ID): PostInterface
    {
        $post = $this->postFactory->create();
        $this->postResource->load($post, $value, $field);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__("Post entity with the \"%1\" $field doesn\'t exist.", $value));
        }

        return $post;
    }

    /**
     * @param PostInterface $post
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
}
