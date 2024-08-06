<?php

namespace ZP\LoyaltyProgram\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterfaceFactory;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResource;
use Magento\Framework\Exception\AlreadyExistsException;

class AddBasicPrograms implements DataPatchInterface
{
    public const PROGRAM_MIN = 1;
    public const PROGRAM_MAX = 2;

    public function __construct(
        private LoyaltyProgramInterfaceFactory $loyaltyProgramFactory,
        private LoyaltyProgramResource $loyaltyProgramResource
    ) {}

    /**
     * @return DataPatchInterface
     * @throws AlreadyExistsException
     */
    public function apply(): DataPatchInterface
    {
        /** @var LoyaltyProgramInterface $programMinimum */
        $programMinimum = $this->loyaltyProgramFactory->create();
        $programMinimum->setProgramName('Program Minimum')
            ->setDescription('Some Description for Program Minimum');
        $this->loyaltyProgramResource->save($programMinimum);

        /** @var LoyaltyProgramInterface $programMaximum */
        $programMaximum = $this->loyaltyProgramFactory->create();
        $programMaximum->setProgramName('Program Maximum')
            ->setDescription('Some Description for Program Maximum');
        $this->loyaltyProgramResource->save($programMaximum);

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
