<?php

namespace Training\Sales\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Sales\Api\Data\FamilyInterface;
use Training\Sales\Api\Data\FamilyInterfaceFactory;
use Training\Sales\Api\FamilyRepositoryInterface;
use Training\Sales\Model\ResourceModel\Family as FamilyResource;

class FamilyRepository implements FamilyRepositoryInterface
{
    private FamilyResource $familyResource;
    private FamilyInterfaceFactory $familyFactory;

    public function __construct(FamilyResource $familyResource, FamilyInterfaceFactory $familyFactory)
    {
        $this->familyResource = $familyResource;
        $this->familyFactory = $familyFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(FamilyInterface $family)
    {
        try {
            $this->familyResource->save($family);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $family;
    }

    /**
     * @inheritDoc
     */
    public function get($value, string $field = FamilyInterface::ENTITY_ID)
    {
        $family = $this->familyFactory->create();
        $this->familyResource->load($family, $value, $field);
        if (!$family->getId()) {
            throw new NoSuchEntityException(__("Family entity with the \"%1\" $field doesn\'t exist.", $value));
        }

        return $family;
    }

    /**
     * @inheritDoc
     */
    public function delete(FamilyInterface $family)
    {
        try {
            $this->familyResource->delete($family);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($familyId)
    {
        return $this->delete($this->get($familyId));
    }
}
