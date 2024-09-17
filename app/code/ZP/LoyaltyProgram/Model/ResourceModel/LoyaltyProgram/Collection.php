<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram as LoyaltyProgramModel;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResource;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class Collection extends AbstractCollection
{
    protected $_idFieldName = LoyaltyProgramInterface::PROGRAM_ID;

    protected function _construct()
    {
        $this->_init(LoyaltyProgramModel::class, LoyaltyProgramResource::class);
    }

    /**
     * @return array[]
     */
    public function getNinBasicProgramsFilter(): array
    {
        return [
            'nin' => [BasicProgramsConfig::PROGRAM_MIN, BasicProgramsConfig::PROGRAM_MAX]
        ];
    }
}
