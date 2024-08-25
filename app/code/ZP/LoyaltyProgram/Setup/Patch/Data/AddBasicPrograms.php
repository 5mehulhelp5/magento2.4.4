<?php

namespace ZP\LoyaltyProgram\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\App\ResourceConnection;

class AddBasicPrograms implements DataPatchInterface
{
    public const PROGRAM_MIN = 1;
    public const PROGRAM_MAX = 2;

    public function __construct(private ResourceConnection $resourceConnection)
    {}

    /**
     * @return DataPatchInterface
     * @throws AlreadyExistsException
     */
    public function apply(): DataPatchInterface
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => self::PROGRAM_MIN,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Program Minimum'
            ]
        );
        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => self::PROGRAM_MAX,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Program Maximum'
            ]
        );

        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => 3,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Bronze',
                LoyaltyProgramInterface::IS_ACTIVE => 1,
                LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '0,1,2,3',
                LoyaltyProgramInterface::ORDER_SUBTOTAL => 1000
            ]
        );

        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => 4,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Silver',
                LoyaltyProgramInterface::IS_ACTIVE => 1,
                LoyaltyProgramInterface::WEBSITE_ID => 1,
                LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '0,1,2,3',
                LoyaltyProgramInterface::ORDER_SUBTOTAL => 2000
            ]
        );

        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => 5,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Gold',
                LoyaltyProgramInterface::IS_ACTIVE => 1,
                LoyaltyProgramInterface::WEBSITE_ID => 1,
                LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '0,1,2',
                LoyaltyProgramInterface::ORDER_SUBTOTAL => 5000
            ]
        );

        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => 6,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Diamant',
                LoyaltyProgramInterface::IS_ACTIVE => 1,
                LoyaltyProgramInterface::WEBSITE_ID => 1,
                LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '0,1,2',
                LoyaltyProgramInterface::ORDER_SUBTOTAL => 10000
            ]
        );

        $connection->insert(
            LoyaltyProgramInterface::MAIN_TABLE,
            [
                LoyaltyProgramInterface::PROGRAM_ID => 7,
                LoyaltyProgramInterface::PROGRAM_NAME => 'Platin',
                LoyaltyProgramInterface::IS_ACTIVE => 1,
                LoyaltyProgramInterface::WEBSITE_ID => 1,
                LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1',
                LoyaltyProgramInterface::ORDER_SUBTOTAL => 20000
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
