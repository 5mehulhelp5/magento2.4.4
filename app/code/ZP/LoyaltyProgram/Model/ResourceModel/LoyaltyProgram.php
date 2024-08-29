<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class LoyaltyProgram extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(LoyaltyProgramInterface::MAIN_TABLE, LoyaltyProgramInterface::PROGRAM_ID);
    }

    /**
     * @param AbstractModel $object
     * @return LoyaltyProgram
     * @throws \Exception
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $programId = (int)$object->getData(LoyaltyProgramInterface::PROGRAM_ID);
        if ($programId === BasicProgramsConfig::PROGRAM_MIN || $programId === BasicProgramsConfig::PROGRAM_MAX) {
            throw new \Exception(
                'You are trying to delete Basic Loyalty Programs! It is forbidden!'
            );
        }

        return parent::_beforeDelete($object);
    }
}
