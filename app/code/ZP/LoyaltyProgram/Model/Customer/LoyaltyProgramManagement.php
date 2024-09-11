<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Customer;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory as ProgramCollectionFactory;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\Collection as ProgramCollection;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;
use ZP\LoyaltyProgram\Console\Command\Customer\AssignLoyaltyProgram as ConsoleCommandConfig;

class LoyaltyProgramManagement implements LoyaltyProgramManagementInterface
{
    private string $result;
    private AdapterInterface $connection;
    private ?LoyaltyProgram $customerProgram = null;
    private ?LoyaltyProgram $programToAssign = null;

    public function __construct(
        private ResourceConnection $resourceConnection,
        private ProgramCollectionFactory $programCollectionFactory
    ) {
        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * @param CustomerInterface $customer
     * @return LoyaltyProgramInterface|null
     */
    public function assignLoyaltyProgram(CustomerInterface $customer): ?LoyaltyProgramInterface
    {
        $customerId = $customer->getId();
        if (!$customerId) {
            throw new \Exception('Customer with id ' . "$customerId" . ', does not exist!');
        } else {
            $customerId = (int)$customerId;
        }

        $grandTotal = $this->getCustomerGrandTotalSum($customerId);
        if (!$grandTotal) {
            $this->result = ConsoleCommandConfig::UNABLE;
            return null;
        }

        /** @var ProgramCollection $programCollection */
        $programCollection = $this->programCollectionFactory->create();
        $programCollection->addFieldToFilter(
            LoyaltyProgram::PROGRAM_ID,
            [
                'nin' => [BasicProgramsConfig::PROGRAM_MIN, BasicProgramsConfig::PROGRAM_MAX]
            ]
        );
        /** @var LoyaltyProgram[] $programs */
        $programs = $programCollection->getItems();
        if(!$this->checkCustomerAndProgramConditions($customer, $programs, $grandTotal)) {
            return null;
        }

        $customerProgramResult = $customer->getExtensionAttributes()->getLoyaltyProgramId();
        if ($customerProgramResult) {
            $programToAssign = $this->programToAssign->getProgramId();
            if ((int)$customerProgramResult !== $programToAssign) {
                $this->executeProgramAssignment(
                    [
                        LoyaltyProgram::PROGRAM_ID => $programToAssign,
                        'customer_email' => $customer->getEmail()
                    ],
                    'customer_id = ' . $customerId
                );

                $this->result = ConsoleCommandConfig::UPDATED;
            } else {
                $this->result = ConsoleCommandConfig::NO_NEED;
            }
        } else {
            $this->executeProgramAssignment(
                [
                    'customer_id' => $customerId,
                    'program_id' => $this->programToAssign->getProgramId(),
                    'customer_email' => $customer->getEmail()
                ]
            );

            $this->result = ConsoleCommandConfig::ASSIGNED;
        }

        $this->customerProgram = $this->programToAssign;
        $this->programToAssign = null;

        return $this->customerProgram;
    }

    public function returnResult(): string
    {
        return $this->result;
    }

    /**
     * @param LoyaltyProgram[] $programs
     * @param float $customerGrandTotal
     * @throws \Exception
     */
    private function validateProgramsToAssign(array $programs, float $customerGrandTotal): void
    {
        foreach ($programs as $programId => $program) {
            $nextProgramId = $program->getNextProgram();
            if (!$nextProgramId) {
                $this->throwProgramException($programId, $program->getProgramName(), LoyaltyProgram::NEXT_PROGRAM);
            }

            $programOrderSubtotal = $this->getLoyaltyProgramOrderSubtotal($program);
            if ($nextProgramId === BasicProgramsConfig::PROGRAM_MAX) {
                $this->programToAssign = $program;
                break;
            } else {
                $nextProgramOrderSubtotal = $this->getLoyaltyProgramOrderSubtotal($programs[$nextProgramId]);
                if ($customerGrandTotal >= $programOrderSubtotal && $customerGrandTotal < $nextProgramOrderSubtotal) {
                    $this->programToAssign = $program;
                    break;
                } elseif ($customerGrandTotal < $programOrderSubtotal && $customerGrandTotal < $nextProgramOrderSubtotal) {
                    $this->result = ConsoleCommandConfig::UNABLE;
                    break;
                }
            }
        }
    }

    private function throwProgramException(int $programId, string $programName, string $field): void
    {
        throw new \Exception(
            'Impossible to manage loyalty program, because program with : ' .
            "ID - '$programId' and ProgramName - ' . '$programName', does not have '$field' data."
        );
    }

    private function getLoyaltyProgramOrderSubtotal(LoyaltyProgram $program): float
    {
        $orderSubtotal = $program->getOrderSubtotal();
        if (!$orderSubtotal) {
            $this->throwProgramException(
                $program->getProgramId(),
                $program->getProgramName(),
                LoyaltyProgram::ORDER_SUBTOTAL
            );
        }

        return (float)$orderSubtotal;
    }

    private function getCustomerGrandTotalSum(int $customerId): ?float
    {
        $grandTotalSum = $this->connection->select()
            ->from(
                'sales_order',
                'SUM(grand_total)'
            )
            ->where('customer_id' . ' = (?)', $customerId);
        $grandTotal = $this->connection->fetchOne($grandTotalSum);
        if (!$grandTotal) {
            $grandTotal = null;
        } else {
            $grandTotal = (float)$grandTotal;
        }

        return $grandTotal;
    }

    private function checkCustomerAndProgramConditions(
        CustomerInterface $customer,
        array $programs,
        float $customerGrandTotal
    ): bool {
        $this->validateProgramsToAssign($programs, $customerGrandTotal);
        if (
            !$this->programToAssign ||
            !$this->validatedForWebsites($customer, $this->programToAssign) ||
            !$this->validateForCustomerGroups($customer, $this->programToAssign)
        ) {
            return false;
        }

        return true;
    }

    private function validatedForWebsites(CustomerInterface $customer, LoyaltyProgram $loyaltyProgram): bool
    {
        $customerWebSite = (int)$customer->getWebsiteId();
        $loyaltyProgramWebsite = $loyaltyProgram->getWebsiteId();
        if (!$customerWebSite || !$loyaltyProgramWebsite || $customerWebSite !== $loyaltyProgramWebsite) {
            return false;
        }

        return true;
    }

    private function validateForCustomerGroups(CustomerInterface $customer, LoyaltyProgram $loyaltyProgram): bool
    {
        $customerGroup = (int)$customer->getGroupId();
        $loyaltyProgramGroups = $loyaltyProgram->getCustomerGroupIds();
        if (!$customerGroup || !$loyaltyProgramGroups || !in_array($customerGroup, $loyaltyProgramGroups)) {
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
}
