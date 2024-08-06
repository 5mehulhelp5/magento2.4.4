<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;

class LoyaltyProgram extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(LoyaltyProgramInterface::MAIN_TABLE, LoyaltyProgramInterface::PROGRAM_ID);
    }
}
