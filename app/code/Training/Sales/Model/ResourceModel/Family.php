<?php

namespace Training\Sales\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Training\Sales\Api\Data\FamilyInterface;

class Family extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(FamilyInterface::MAIN_TABLE, FamilyInterface::ENTITY_ID);
    }
}
