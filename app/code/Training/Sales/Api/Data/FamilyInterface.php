<?php
declare(strict_types=1);

namespace Training\Sales\Api\Data;

interface FamilyInterface
{
    public const ENTITY_ID = 'entity_id';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const AGE = 'age';
    public const ROLE = 'role';
    public const CREATION_TIME = 'creation_time';
    public const UPDATE_TIME = 'update_time';
    public const IS_ACTIVE = 'is_active';

    public const MAIN_TABLE = 'family';

    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @return int
     */
    public function getAge(): int;

    /**
     * @return string
     */
    public function getRole(): string;

    /**
     * @return string
     */
    public function getCreationTime(): string;

    /**
     * @return string
     */
    public function getUpdateTime(): string;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName): self;

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName): self;

    /**
     * @param int $age
     * @return $this
     */
    public function setAge(int $age): self;

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role): self;

    /**
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime(string $creationTime): self;

    /**
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime(string $updateTime): self;


    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self;
}
