<?php

namespace VConnect\Blog\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use VConnect\Blog\Api\Data\PostInterface;

class AddTestPostRecordToTable implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply(): DataPatchInterface
    {
        $tableName = $this->moduleDataSetup->getTable(PostInterface::MAIN_TABLE);
        $this->moduleDataSetup->getConnection()
            ->insert(
                $tableName,
                [
                    'title' => 'test post',
                    'content' => 'test post content data',
                    'announce' => 'test post announce data (brief description data)'
                ]
            );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }


}
