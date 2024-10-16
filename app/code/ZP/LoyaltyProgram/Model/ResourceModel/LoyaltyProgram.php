<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use ZP\LoyaltyProgram\Api\Model\Validators\Controller\Adminhtml\Program\ValidatorInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram\CustomerProgramManagement;
use ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram\CustomerProgramManagementFactory;
use ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram\SalesRuleProgramsManagement;
use ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram\SalesRuleProgramsManagementFactory;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;
use ZP\LoyaltyProgram\Model\LoyaltyProgram as LoyaltyProgramModel;

class LoyaltyProgram extends AbstractDb
{
    private const EDIT = 'edit';
    private const DELETE = 'delete';
    private ?int $programId = null;
    private string $actionForException;
    private ?bool $isActiveStatusUpdatedToDisable = null;
    private ?CustomerProgramManagement $customerProgramManagement = null;
    private ?SalesRuleProgramsManagement $salesRuleProgramsManagement = null;

    public function __construct(
        Context $context,
        private ValidatorInterface $dataValidator,
        private CustomerProgramManagementFactory $customerProgramManagementFactory,
        private SalesRuleProgramsManagementFactory $salesRuleProgramsManagementFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    public function beforeAction(): void
    {
        if ($this->customerProgramManagement) {
            $this->customerProgramManagement->collectCustomersFromProgram($this->programId);
        }

        if ($this->salesRuleProgramsManagement) {
            $this->salesRuleProgramsManagement->collectRules($this->programId);
        }
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function afterAction(): void
    {
        if ($this->customerProgramManagement && $this->customerProgramManagement->getCustomersCount()) {
            if ($this->getActiveProgramsCount()) {
                $this->customerProgramManagement->reassignProgramToCustomers();
            } else {
                $this->customerProgramManagement->deleteProgramFromCustomers();
            }
        }

        if ($this->salesRuleProgramsManagement && $this->salesRuleProgramsManagement->getRulesCount()) {
            $this->salesRuleProgramsManagement->deleteProgramFromSalesRules();
        }
    }

    public function setIsActiveStatusUpdatedToDisable(bool $bool): void
    {
        $this->isActiveStatusUpdatedToDisable = $bool;
    }

    public function createManagers(): void
    {
        $this->customerProgramManagement = $this->customerProgramManagementFactory->create();
        if ($this->actionForException === self::DELETE) {
            $this->salesRuleProgramsManagement = $this->salesRuleProgramsManagementFactory->create();
        }
    }

    public function destroyManagers(): void
    {
        $this->customerProgramManagement = null;
        $this->salesRuleProgramsManagement = null;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getActiveProgramsCount(): int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                LoyaltyProgramModel::MAIN_TABLE,
                'COUNT(' . LoyaltyProgramModel::PROGRAM_ID . ')'
            )->where(
                LoyaltyProgramModel::PROGRAM_ID,
                [BasicProgramsConfig::PROGRAM_MIN, BasicProgramsConfig::PROGRAM_MAX],
                'nin'
            )->where(
                LoyaltyProgramModel::IS_ACTIVE . ' = ' . LoyaltyProgramModel::ACTIVE
            );
        $result = $connection->fetchOne($select);
        if ($result === false || $result === null) {
            throw new \Exception(
                'Sql Problem during COUNT \'' . LoyaltyProgramModel::PROGRAM_ID . '\'' .
                ' from \'' . LoyaltyProgramModel::MAIN_TABLE . '\' table!'
            );
        }

        return (int)$result;
    }

    protected function _construct()
    {
        $this->_init(LoyaltyProgramModel::MAIN_TABLE, LoyaltyProgramModel::PROGRAM_ID);
    }

    /**
     * @param AbstractModel $object
     * @return LoyaltyProgram
     * @throws \Exception
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $this->actionForException = self::DELETE;
        $programId = $this->getProgramId($object);
        $this->validateProgramId($programId);
        $this->createManagers();
        $this->beforeAction();

        return parent::_beforeDelete($object);
    }

    /**
     * @param AbstractModel $object
     * @return LoyaltyProgram
     * @throws \Exception
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $programId = $this->getProgramId($object);
        if ($programId !== null) {
            $this->actionForException = self::EDIT;
            $this->validateProgramId($programId);
        }

        if ($this->isActiveStatusUpdatedToDisable) {
            $this->createManagers();
        }

        $this->beforeAction();


        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return LoyaltyProgram
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->afterAction();
        $this->destroyManagers();

        return parent::_afterDelete($object);
    }

    /**
     * @param AbstractModel $object
     * @return LoyaltyProgram
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->afterAction();
        $this->destroyManagers();
        $this->isActiveStatusUpdatedToDisable = null;

        return parent::_afterSave($object);
    }

    /**
     * @param int|string|null $programId
     * @throws \Exception
     */
    private function validateProgramId(int|string|null $programId): void
    {
        $this->programId = $this->dataValidator->validateProgramId($programId);
        if ($this->dataValidator->isBasicProgram($this->programId)) {
            throw new \Exception(
                'Someone trying to ' . $this->actionForException . ' Basic Loyalty Programs! It is forbidden!'
            );
        }
    }

    private function getProgramId(AbstractModel $object): int|string|null
    {
        return $object->getData(LoyaltyProgramModel::PROGRAM_ID);
    }
}
