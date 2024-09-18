<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Console\Command\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramManagementInterface;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class AssignLoyaltyProgram extends Command
{
    public const REMOVED = 'removed';
    public const ASSIGNED = 'assigned';
    public const UPDATED = 'updated';
    public const UNABLE = 'unable';
    public const NO_NEED = 'no_need';
    public const NOT_EXIST = 'not_exist';
    public const WRONG_DATA = 'wrong_data';
    private const REMOVED_MSG = 'Program(s) removed of such customer(s) id(s) : ';
    private const ASSIGNED_MSG = 'Assigned program(s) to customer(s) with id(s) : ';
    private const UPDATED_MSG = 'Updated programs(s) to customer(s) with id(s) : ';
    private const UNABLE_MSG = 'Unable to assign program(s) to customer(s) with id(s) : ';
    private const NO_NEED_MSG = 'No need to assign program(s) to customer(s) with id(s) : ';
    private const NOT_EXIST_MSG = 'Customer(s) with such id(s) not exist : ';
    private const WRONG_DATA_MSG = 'Data type of this customer(s) id(s) not correct : ';

    private array $results = [
        self::ASSIGNED => [],
        self::UPDATED => [],
        self::UNABLE => [],
        self::NO_NEED => [],
        self::NOT_EXIST => [],
        self::WRONG_DATA => [],
        self::REMOVED => []
    ];

    private array $resultMsgs = [
        self::ASSIGNED => self::ASSIGNED_MSG,
        self::UPDATED => self::UPDATED_MSG,
        self::UNABLE => self::UNABLE_MSG,
        self::NO_NEED => self::NO_NEED_MSG,
        self::NOT_EXIST => self::NOT_EXIST_MSG,
        self::WRONG_DATA => self::WRONG_DATA_MSG,
        self::REMOVED => self::REMOVED_MSG
    ];

    private string $resultMsg = 'Result is : ' . "\n";

    /**
     * @param string|null $name
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private CustomerCollectionFactory $customerCollectionFactory,
        private LoyaltyProgramManagementInterface $loyaltyProgramManagement,

        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'A command to assign loyalty program to customer(s).'
        );

        $this->addArgument(
            'customer_ids',
            InputArgument::IS_ARRAY,
            'Separate multiple ids with a space.'
        );

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $customerIds = $input->getArgument('customer_ids');
            if (!$this->isAllCustomers($customerIds)) {
                $checkedCustomerIds = $this->checkDataType($customerIds);
                if (!$checkedCustomerIds && $this->results[self::WRONG_DATA]) {
                    $this->prepareResultMessage(self::WRONG_DATA);
                    $output->writeln($this->resultMsg);

                    return;
                }
            } else {
                $checkedCustomerIds = [];
            }

            if (count($checkedCustomerIds) !== 1) {
                if (!$this->isAllCustomers($customerIds)) {
                    $searchCriteria = $this->searchCriteriaBuilder
                        ->addFilter('entity_id', $checkedCustomerIds, 'in')
                        ->create();
                    $customerIds = $checkedCustomerIds;
                } else {
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                }

                $customers = $this->customerRepository->getList($searchCriteria)->getItems();
            } else {
                $customers[] = $this->customerRepository->getById($checkedCustomerIds[array_key_first($checkedCustomerIds)]);
            }

            if (!$this->checkCustomersCount($customers, $customerIds, $output) && !$customers) {
                if ($this->results[self::WRONG_DATA]) {
                    $this->prepareResultMessage(self::WRONG_DATA);
                }

                $this->prepareResultMessage(self::NOT_EXIST);
                $output->writeln($this->resultMsg);

                return;
            }

            $this->assignLoyaltyProgramsToCustomers($customers);
            $this->prepareResultMessage(self::REMOVED);
            $this->prepareResultMessage(self::ASSIGNED);
            $this->prepareResultMessage(self::UPDATED);
            $this->prepareResultMessage(self::UNABLE);
            $this->prepareResultMessage(self::NO_NEED);
            $this->prepareResultMessage(self::NOT_EXIST);
            $this->prepareResultMessage(self::WRONG_DATA);
            $output->writeln($this->resultMsg);
        } catch (\Exception $exception) {
            $output->write($exception->getMessage());
            parent::execute($input, $output);
        }
    }

    private function isAllCustomers(array $data): bool
    {
        return !$data;
    }

    private function checkDataType(array $customerIds): array
    {
        foreach ($customerIds as $key => $customerId) {
            if (!$this->isCorrectDataType($customerId)) {
                $this->results[self::WRONG_DATA][$customerId] = $customerId;
                unset($customerIds[$key]);
            } else {
                $customerIds[$key] = (int)$customerId;
            }
        }

        return $customerIds;
    }

    private function isCorrectDataType($data): bool
    {
        if (!is_numeric($data) || !$this->isInteger($data)) {
            return false;
        }

        return true;
    }

    private function isInteger($data): bool
    {
        preg_match('/\./', (string)$data, $matches);
        if ($matches) {
            return false;
        }

        return true;
    }

    private function checkCustomersCount(
        array $customers,
        array $customersIdsFromCommand,
        OutputInterface $output
    ): bool {
        $result = true;
        $countCustomersFromCollection = count($customers);
        $countCustomersFromCommand = count($customersIdsFromCommand);
        $isAllCustomers = $this->isAllCustomers($customersIdsFromCommand);
        if ($isAllCustomers && !$customers) {
            $result = false;
            $output->writeln('We dont have any customers at this moment.');
        } elseif (!$isAllCustomers && !$customers) {
            $result = false;
            foreach ($customersIdsFromCommand as $customerId) {
                $this->results[self::NOT_EXIST][$customerId] = $customerId;
            }
        } elseif (!$isAllCustomers && $countCustomersFromCollection !== $countCustomersFromCommand) {
            $customersIdsFromCollection = [];
            /** @var Customer $customer */
            foreach ($customers as $customer) {
                $customerId = (int)$customer->getId();
                $customersIdsFromCollection[$customerId] = $customerId;
            }

            foreach ($customersIdsFromCommand as $customerId) {
                if (!in_array($customerId, $customersIdsFromCollection)) {
                    $this->results[self::NOT_EXIST][$customerId] = $customerId;
                }
            }
        }

        return $result;
    }

    private function prepareResultMessage(string $resultType): void
    {
        if ($this->results[$resultType]) {
            $this->resultMsg .= $this->resultMsgs[$resultType];
            $resultCount = count($this->results[$resultType]);
            $i = 1;
            foreach ($this->results[$resultType] as $customerId) {
                $this->resultMsg .= $customerId;
                if ($i !== $resultCount) {
                    $this->resultMsg .= ', ';
                } else {
                    $this->resultMsg .= '.' . "\n";
                }

                $i++;
            }
        }
    }

    /**
     * @param Customer[] $customers
     * @throws \Exception
     */
    private function assignLoyaltyProgramsToCustomers(array $customers): void
    {
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $this->loyaltyProgramManagement->assignLoyaltyProgram($customer);
            $result = $this->loyaltyProgramManagement->returnResult();
            $customerId = (int)$customer->getId();
            $this->results[$result][$customerId] = $customerId;
        }
    }
}
