<?php
declare(strict_types=1);

namespace VConnect\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use VConnect\Blog\Api\Data\PostInterface;

class Post extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(PostInterface::MAIN_TABLE, PostInterface::ENTITY_ID);
    }
}
