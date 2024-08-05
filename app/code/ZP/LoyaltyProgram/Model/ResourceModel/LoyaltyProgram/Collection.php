<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram as LoyaltyProgramModel;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = LoyaltyProgramInterface::PROGRAM_ID;

    protected function _construct()
    {
        $this->_init(LoyaltyProgramModel::class, LoyaltyProgramResource::class);
    }
}
