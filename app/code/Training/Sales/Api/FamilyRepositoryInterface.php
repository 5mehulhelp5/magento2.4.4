<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Training\Sales\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Training\Sales\Api\Data\FamilyInterface;

/**
 * Family entity CRUD interface.
 * @api
 */
interface FamilyRepositoryInterface
{
    /**
     * @param \Training\Sales\Api\Data\FamilyInterface $family
     * @return \Training\Sales\Api\Data\FamilyInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Training\Sales\Api\Data\FamilyInterface $family);

    /**
     * @param $value
     * @param string $field
     * @return \Training\Sales\Api\Data\FamilyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($value, string $field = FamilyInterface::ENTITY_ID);

//    /**
//     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
//     * @return \Magento\Cms\Api\Data\PageSearchResultsInterface
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Training\Sales\Api\Data\FamilyInterface $family
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Training\Sales\Api\Data\FamilyInterface $family);

    /**
     * @param int $familyId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($familyId);
}
