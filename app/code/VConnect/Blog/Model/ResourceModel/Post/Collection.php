<?php

namespace VConnect\Blog\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Model\Post as PostModel;
use VConnect\Blog\Model\ResourceModel\Post as PostResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = PostInterface::ENTITY_ID;

    protected function _construct()
    {
        $this->_init(PostModel::class, PostResource::class);
    }
}
