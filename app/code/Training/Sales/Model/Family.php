<?php
declare(strict_types=1);

namespace Training\Sales\Model;

use Training\Sales\Api\Data\FamilyInterface;
use Magento\Framework\Model\AbstractModel;

class Family extends AbstractModel implements FamilyInterface
{

    /**
     * @inheritDoc
     */
    public function getFirstName(): string
    {
        return $this->getData(self::FIRST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): string
    {
        return $this->getData(self::LAST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getAge(): int
    {
        return $this->getData(self::AGE);
    }

    /**
     * @inheritDoc
     */
    public function getRole(): string
    {
        return $this->getData(self::ROLE);
    }

    /**
     * @inheritDoc
     */
    public function getCreationTime(): string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateTime(): string
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): bool
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @param string $firstName
     * @return FamilyInterface
     */
    public function setFirstName(string $firstName): FamilyInterface
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * @inheritDoc
     */
    public function setLastName(string $lastName): FamilyInterface
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * @inheritDoc
     */
    public function setAge(int $age): FamilyInterface
    {
        return $this->setData(self::AGE, $age);
    }

    /**
     * @inheritDoc
     */
    public function setRole(string $role): FamilyInterface
    {
        return $this->setData(self::ROLE, $role);
    }

    /**
     * @inheritDoc
     */
    public function setCreationTime(string $creationTime): FamilyInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @inheritDoc
     */
    public function setUpdateTime(string $updateTime): FamilyInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $isActive): FamilyInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Training\Sales\Model\ResourceModel\Family::class);
    }
}
