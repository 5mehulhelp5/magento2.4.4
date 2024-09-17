<?php

namespace Training\Sales\Model\ResourceModel\Family;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Sales\Api\Data\FamilyInterface;
use Training\Sales\Model\Family as FamilyModel;
use Training\Sales\Model\ResourceModel\Family as FamilyResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FamilyInterface::ENTITY_ID;

    protected function _construct()
    {
        $this->_init(FamilyModel::class, FamilyResource::class);
    }
}
