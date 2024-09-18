<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Customer;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory as ProgramCollectionFactory;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\Collection as ProgramCollection;
use ZP\LoyaltyProgram\Console\Command\Customer\AssignLoyaltyProgram as ConsoleCommandConfig;
use Magento\Quote\Model\Quote;
use ZP\LoyaltyProgram\Model\Config as ProgramScopeConfig;
use Psr\Log\LoggerInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class LoyaltyProgramManagement implements LoyaltyProgramManagementInterface
{
    private string $result;
    private ?int $customerId = null;
    private AdapterInterface $connection;
    private ?LoyaltyProgram $customerProgram = null;
    private ?LoyaltyProgram $programToAssign = null;

    public function __construct(
        private ResourceConnection $resourceConnection,
        private ProgramCollectionFactory $programCollectionFactory,
        private ProgramScopeConfig $programScopeConfig,
        private LoyaltyProgramRepositoryInterface $loyaltyProgramRepository,
        private LoggerInterface $logger
    ) {
        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return LoyaltyProgramInterface|null
     */
    public function assignLoyaltyProgram(\Magento\Customer\Api\Data\CustomerInterface $customer): ?LoyaltyProgramInterface
    {
        $customerId = $customer->getId();
        if (!$customerId) {
            throw new \Exception('Customer with id ' . "$customerId" . ', does not exist!');
        } else {
            $this->customerId = (int)$customerId;
        }

        $customerProgramResult = $customer->getExtensionAttributes()->getLoyaltyProgramId();
        if (!$customerProgramResult) {
            $select = $this->connection->select()
                ->from('zp_loyalty_program_customer', 'program_id')
                ->where('customer_id = ?', $this->customerId);
            $customerProgramResult = $this->connection->fetchOne($select);
        }

        if (!$this->checkProgramConditions($customer, (int)$customer->getWebsiteId(), (int)$customer->getGroupId())) {
            if (!$this->customerProgram && $customerProgramResult) {
                $this->connection->delete('zp_loyalty_program_customer', 'customer_id = ' . $this->customerId);
                $this->result = ConsoleCommandConfig::REMOVED;
            }

            return $this->getCustomerProgram();
        }

        if ($customerProgramResult) {
            $programIdToAssign = (int)$this->programToAssign->getProgramId();
            if ((int)$customerProgramResult !== $programIdToAssign) {
                $this->executeProgramAssignment(
                    [
                        LoyaltyProgram::PROGRAM_ID => $programIdToAssign,
                        'customer_email' => $customer->getEmail()
                    ],
                    'customer_id = ' . $this->customerId
                );

                $this->result = ConsoleCommandConfig::UPDATED;
            } else {
                $this->result = ConsoleCommandConfig::NO_NEED;
            }
        } else {
            $this->executeProgramAssignment(
                [
                    'customer_id' => $this->customerId,
                    'program_id' => $this->programToAssign->getProgramId(),
                    'customer_email' => $customer->getEmail()
                ]
            );

            $this->result = ConsoleCommandConfig::ASSIGNED;
        }

        $this->programToAssign = null;

        return $this->getCustomerProgram();
    }

    public function returnResult(): string
    {
        return $this->result;
    }

    /**
     * @param LoyaltyProgram[] $programs
     * @param float $entityGrandTotal
     * @throws \Exception
     */
    private function validateProgramsToAssign(array $programs, float $entityGrandTotal): void
    {
        if (count($programs) === 1) {
            $program = $programs[array_key_first($programs)];
            $programOrderSubtotal = $this->getProgramOrderSubtotal($program);
            if ($entityGrandTotal >= $programOrderSubtotal) {
                $this->programToAssign = $program;
            }
        } else {
            $programIdsArray = [];
            foreach ($programs as $programId => $program) {
                $programIdsArray[$programId] = $programId;
            }

            foreach ($programs as $programId => $program) {
                if ($programIdsArray) {
                    unset($programIdsArray[$programId]);
                }

                $programOrderSubtotal = $this->getProgramOrderSubtotal($program);

                $nextProgramId = $program->getNextProgram();
                if ($nextProgramId) {
                    if (array_key_exists($nextProgramId, $programs)) {
                        $nextProgramOrderSubtotal = $this->getProgramOrderSubtotal($programs[$nextProgramId]);
                    } else {
                        try {
                            $nextProgramOrderSubtotal = $this->getProgramOrderSubtotal(
                                $this->loyaltyProgramRepository->get($nextProgramId)
                            );
                        } catch (NoSuchEntityException $exception) {
                            $nextProgramOrderSubtotal = null;
                        }

                        if ($nextProgramOrderSubtotal === null) {
                            $nextProgramOrderSubtotal = $this->getNextProgramOrderSubtotal($programIdsArray, $programs);
                        }
                    }
                } else {
                    $nextProgramOrderSubtotal = $this->getNextProgramOrderSubtotal($programIdsArray, $programs);
                    $this->addLogMsg($program, LoyaltyProgram::NEXT_PROGRAM);
                }

                if ($nextProgramOrderSubtotal === null ) {
                    if ($entityGrandTotal >= $programOrderSubtotal) {
                        $this->programToAssign = $program;
                        break;
                    }
                } else {
                    if ($entityGrandTotal >= $programOrderSubtotal && $entityGrandTotal < $nextProgramOrderSubtotal) {
                        $this->programToAssign = $program;
                        break;
                    } elseif ($entityGrandTotal < $programOrderSubtotal && $entityGrandTotal < $nextProgramOrderSubtotal) {
//                        $this->result = ConsoleCommandConfig::UNABLE;
                        break;
                    }
                }
            }
        }
    }

    private function getProgramOrderSubtotal(LoyaltyProgram $program): ?float
    {
        $orderSubtotal = $program->getOrderSubtotal();
        $programId = $program->getProgramId();
        if (
            !$orderSubtotal &&
            $programId !== BasicProgramsConfig::PROGRAM_MIN &&
            $programId !== BasicProgramsConfig::PROGRAM_MAX
        ) {
            $this->addLogMsg($program, LoyaltyProgram::ORDER_SUBTOTAL);
        }

        return $orderSubtotal ? (float)$orderSubtotal : null;
    }

    private function getCustomerGrandTotalSum(int $websiteId): float
    {
        $stateValue = 'new';
        if ($this->programScopeConfig->isApplySubtotalChangesAfterInvoice($websiteId)) {
            $stateValue = 'processing';
        }

        $grandTotalSum = $this->connection->select()
            ->from(
                'sales_order',
                'SUM(grand_total)'
            )
            ->where(
                'customer_id = ?', $this->customerId
            )->where('state = ?', $stateValue);
        $grandTotal = $this->connection->fetchOne($grandTotalSum);
        if (!$grandTotal) {
            $grandTotal = 0.0;
        } else {
            $grandTotal = (float)$grandTotal;
        }

        return $grandTotal;
    }

    public function checkProgramConditions(
        CustomerInterface|Quote $entity,
        int $webSiteId,
        int $customerGroupId
    ): bool {
        if (!$this->programScopeConfig->isEnabled($webSiteId)) {
            return false;
        }

        if ($entity instanceof CustomerInterface) {
            $grandTotal = $this->getCustomerGrandTotalSum($webSiteId);
        } else {
            $grandTotal = (float)$entity->getGrandTotal();
        }

        /** @var ProgramCollection $programCollection */
        $programCollection = $this->programCollectionFactory->create();
        $programCollection->addFieldToFilter(
            LoyaltyProgram::PROGRAM_ID,
            $programCollection->getNinBasicProgramsFilter()
        );
        $programCollection->addFieldToFilter(LoyaltyProgram::IS_ACTIVE, 1);
        $programCollection->addFieldToFilter(LoyaltyProgram::ORDER_SUBTOTAL, ['neq' => 'NULL']);
        $programCollection->setOrder(LoyaltyProgram::ORDER_SUBTOTAL, $programCollection::SORT_ORDER_ASC);
        /** @var LoyaltyProgram[] $programs */
        $programs = $programCollection->getItems();
        if (!$programs) {
            return false;
        }

        $this->validateProgramsToAssign($programs, $grandTotal);
        if (
            !$this->programToAssign ||
            !$this->validatedForWebsites($webSiteId, $this->programToAssign) ||
            !$this->validateForCustomerGroups($customerGroupId, $this->programToAssign)
        ) {
            $this->result = ConsoleCommandConfig::UNABLE;
            $this->setCustomerProgram($this->programToAssign);

            return false;
        }

        $this->setCustomerProgram($this->programToAssign);

        return true;
    }

    private function validatedForWebsites(int $customerWebSiteId, LoyaltyProgram $loyaltyProgram): bool
    {
        $loyaltyProgramWebsite = $loyaltyProgram->getWebsiteId();
        if (!$customerWebSiteId || !$loyaltyProgramWebsite || $customerWebSiteId !== $loyaltyProgramWebsite) {
            return false;
        }

        return true;
    }

    private function validateForCustomerGroups(int $customerGroupId, LoyaltyProgram $loyaltyProgram): bool
    {
        $loyaltyProgramGroups = $loyaltyProgram->getCustomerGroupIds();
        if (!$loyaltyProgramGroups || !in_array((string)$customerGroupId, $loyaltyProgramGroups)) {
            return false;
        }

        return true;
    }

    private function executeProgramAssignment(array $data, string $condition = null): void
    {
        $tableName = 'zp_loyalty_program_customer';
        if ($condition === null) {
            $this->connection->insert($tableName, $data);
        } else {
            $this->connection->update($tableName, $data, $condition);
        }
    }

    private function setCustomerProgram(?LoyaltyProgramInterface $loyaltyProgram = null): void
    {
        $this->customerProgram = $loyaltyProgram;
    }

    public function getCustomerProgram(): ?LoyaltyProgramInterface
    {
        return $this->customerProgram;
    }

    /**
     * @param array $programIds
     * @param LoyaltyProgram[] $programs
     * @return float|null
     */
    private function getNextProgramOrderSubtotal(array $programIds, array $programs): ?float
    {
        return $programIds ? $this->getProgramOrderSubtotal($programs[array_key_first($programIds)]) : null;
    }

    private function addLogMsg(LoyaltyProgram $program, string $field): void
    {
        $this->logger->notice(
            'Program \'' . $program->getProgramName() . '\' ' .
            'with id : ' . '\'' . $program->getProgramId() . '\', doesn\'t have ' . '\'' . $field . '\' ' . 'data, ' .
            'it is NULL. Update this program please!'
        );
    }
}
