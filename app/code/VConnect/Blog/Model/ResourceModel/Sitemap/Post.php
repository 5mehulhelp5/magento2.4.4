<?php
declare(strict_types=1);

namespace VConnect\Blog\Model\ResourceModel\Sitemap;

use Magento\Framework\DataObject;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Model\Post as PostModel;
use VConnect\Blog\Model\PostFactory;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Post extends AbstractDb
{
    /**
     * Post constructor.
     * @param \VConnect\Blog\Model\PostFactory $postFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        private PostFactory $postFactory,
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getCollection(): array
    {
        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), PostInterface::URL_KEY, PostInterface::UPDATED_AT]
        )->where(
            'main_table.publish = 1'
        );

        $query = $this->getConnection()->query($select);

        $posts = [];
        while ($row = $query->fetch()) {
            /** @var PostModel $post */
            $post = $this->postFactory->create();
            $post->setData($row);
            $post = $this->_prepareObject($post);

            /** @var DataObject $post */
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
