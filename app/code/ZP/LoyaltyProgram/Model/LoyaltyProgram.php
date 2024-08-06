<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model;

use Magento\Framework\Model\AbstractModel;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResourceModel;

class LoyaltyProgram extends AbstractModel implements LoyaltyProgramInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LoyaltyProgramResourceModel::class);
    }

    /**
     * @return int
     */
    public function getProgramId(): int
    {
        return (int)$this->getData(self::PROGRAM_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProgramId(int|string $programId): LoyaltyProgramInterface
    {
        return $this->setData(self::PROGRAM_ID, (int)$programId);
    }

    /**
     * @return string|null
     */
    public function getProgramName(): ?string
    {
        return $this->getData(self::PROGRAM_NAME);
    }

    /**
     * @param string $programName
     * @return LoyaltyProgramInterface
     */
    public function setProgramName(string $programName): LoyaltyProgramInterface
    {
        return $this->setData(self::PROGRAM_NAME, $programName);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return LoyaltyProgramInterface
     */
    public function setIsActive(bool $isActive): LoyaltyProgramInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return LoyaltyProgramInterface
     */
    public function setDescription(string $description): LoyaltyProgramInterface
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return string|null
     */
    public function getConditionsSerialized(): ?string
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @param string $conditionsSerialized
     * @return LoyaltyProgramInterface
     */
    public function setConditionsSerialized(string $conditionsSerialized): LoyaltyProgramInterface
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @return int|null
     */
    public function getPreviousProgram(): ?int
    {
        return (int)$this->getData(self::PREVIOUS_PROGRAM);
    }

    /**
     * @param int $previousProgram
     * @return LoyaltyProgramInterface
     */
    public function setPreviousProgram(int $previousProgram): LoyaltyProgramInterface
    {
        return $this->setData(self::PREVIOUS_PROGRAM, $previousProgram);
    }

    /**
     * @return int|null
     */
    public function getNextProgram(): ?int
    {
        return (int)$this->getData(self::NEXT_PROGRAM);
    }

    /**
     * @param int $nextProgram
     * @return LoyaltyProgramInterface
     */
    public function setNextProgram(int $nextProgram): LoyaltyProgramInterface
    {
        return $this->setData(self::NEXT_PROGRAM, $nextProgram);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string|null $createdAt
     * @return LoyaltyProgramInterface
     */
    public function setCreatedAt(?string $createdAt = null): LoyaltyProgramInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string|null $updatedAt
     * @return LoyaltyProgramInterface
     */
    public function setUpdatedAt(?string $updatedAt = null): LoyaltyProgramInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
