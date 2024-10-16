<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Model\ResourceModel\LoyaltyProgram;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use ZP\LoyaltyProgram\Model\Prepare\Data\DataPreparer;
use ZP\LoyaltyProgram\Model\Configs\Customer\Program\Config as CustomerProgramConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerProgramManagement
{
    private AdapterInterface $connection;
    private array $programCustomerIds = [];
    private int $countCustomers = 0;

    public function __construct(
        ResourceConnection $resourceConnection,
        private DataPreparer $prepareData,
        private CustomerRepositoryInterface $customerRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private LoyaltyProgramManagementInterface $programManagement
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    public function collectCustomersFromProgram(int $programId): void
    {
        $this->programCustomerIds = $this->prepareData->makeArrayValuesLikeKeys($this->selectCustomerIds($programId));
        $this->countCustomers = count($this->programCustomerIds);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function reassignProgramToCustomers(): void
    {
        if ($this->programCustomerIds) {
            if ($this->countCustomers === 1) {
                $customers[] = $this->customerRepository->getById(array_key_first($this->programCustomerIds));
            } else {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('entity_id', $this->programCustomerIds, 'in')
                    ->create();
                $customers = $this->customerRepository->getList($searchCriteria)->getItems();
            }

            foreach ($customers as $customer) {
                $this->programManagement->assignLoyaltyProgram($customer);
            }
        }
    }

    public function deleteProgramFromCustomers(): void
    {
        if ($this->programCustomerIds) {
            $customerIds = implode(',', $this->programCustomerIds);
            $this->connection->delete(
                CustomerProgramConfig::CUSTOMER_PROGRAM_TABLE,
                CustomerProgramConfig::CUSTOMER_ID . ' IN (' . $customerIds . ')'
            );
        }
    }

    private function selectCustomerIds(int $programId): array
    {
        /** @var  Select $select */
        $select = $this->connection->select()
            ->from(CustomerProgramConfig::CUSTOMER_PROGRAM_TABLE, CustomerProgramConfig::CUSTOMER_ID)
            ->where(CustomerProgramConfig::PROGRAM_ID . ' = ' . $programId);

        return $this->connection->fetchAssoc($select);
    }

    public function getCustomersCount(): int
    {
        return $this->countCustomers;
    }
}
