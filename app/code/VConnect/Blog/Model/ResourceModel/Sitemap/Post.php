<?php
declare(strict_types=1);

namespace VConnect\Blog\Model\ResourceModel\Sitemap;

use Magento\Framework\DataObject;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Model\Post as PostModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use VConnect\Blog\Model\ResourceModel\Post\CollectionFactory;

class Post extends AbstractDb
{
    /**
     * Post constructor.
     * @param \VConnect\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        private CollectionFactory $postCollectionFactory,
        Context $context,
        string $connectionName = null,
    ) {
        parent::__construct($context, $connectionName);
    }


    protected function _construct()
    {
        $this->_init(PostInterface::MAIN_TABLE, PostInterface::ENTITY_ID);
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        $collection = $this->postCollectionFactory->create();
        $collection->addFilter('publish', true);
        $postsCollection = $collection->getItems();

        $posts = [];
        /** @var PostModel $postModel */
        foreach ($postsCollection as $postModel) {
            $post = $this->_prepareObject($postModel);
            $posts[$post->getId()] = $post;
        }

        return $posts;
    }

    /**
     * @param PostModel $post
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareObject(PostModel $post): DataObject
    {
        $object = new DataObject();
        $object->setId($post->getEntityId());
        $object->setUrl($post->getUrl());
        $object->setUpdatedAt($post->getUpdatedAt());

        return $object;
    }
}
