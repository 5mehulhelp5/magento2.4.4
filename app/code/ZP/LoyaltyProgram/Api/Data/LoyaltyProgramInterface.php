<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Api\Data;

interface LoyaltyProgramInterface
{
    public const MAIN_TABLE = 'zp_loyalty_program';
    public const PROGRAM_ID = 'program_id';
    public const PROGRAM_NAME = 'program_name';
    public const IS_ACTIVE = 'is_active';
    public const DESCRIPTION = 'description';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';
    public const PREVIOUS_PROGRAM = 'previous_program';
    public const NEXT_PROGRAM = 'next_program';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const WEBSITE_ID = 'website_id';
    public const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    public const ORDER_SUBTOTAL = 'order_subtotal';
    public const IS_PROGRAM_MINIMUM = 'is_program_minimum';
    public const IS_PROGRAM_MAXIMUM = 'is_program_maximum';

    /**
     * @return int
     */
    public function getProgramId(): int;

    /**
     * @param int|string $programId
     * @return $this
     */
    public function setProgramId(int|string $programId): self;

    /**
     * @return string|null
     */
    public function getProgramName(): ?string;

    /**
     * @param string $programName
     * @return $this
     */
    public function setProgramName(string $programName): self;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self;

    /**
     * @return string|null
     */
    public function getConditionsSerialized(): ?string;

    /**
     * @param string $conditionsSerialized
     * @return $this
     */
    public function setConditionsSerialized(string $conditionsSerialized): self;

    /**
     * @return int|null
     */
    public function getPreviousProgram(): ?int;

    /**
     * @param int $previousProgram
     * @return $this
     */
    public function setPreviousProgram(int $previousProgram): self;

    /**
     * @return int|null
     */
    public function getNextProgram(): ?int;

    /**
     * @param int $nextProgram
     * @return $this
     */
    public function setNextProgram(int $nextProgram): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?string $createdAt = null): self;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?string $updatedAt = null): self;

    /**
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId(?int $websiteId): self;

    /**
     * @return array
     */
    public function getCustomerGroupIds(): array;

    /**
     * @param $customerGroupIds mixed
     * @return $this
     */
    public function setCustomerGroupIds(mixed $customerGroupIds): self;

    /**
     * @return int|null
     */
    public function getOrderSubtotal(): ?int;

    /**
     * @param int|null $orderSubtotal
     * @return $this
     */
    public function setOrderSubtotal(?int $orderSubtotal): self;
}
