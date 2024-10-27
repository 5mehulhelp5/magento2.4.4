<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Observer\Customer\Model\ResourceModel\CustomerRepository;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Model\Configs\Program\Scope\Config as ProgramScopeConfig;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Customer\Model\Data\Customer;
use ZP\LoyaltyProgram\Model\Configs\Customer\Program\Config as CustomerProgramConfig;

class ChangeCustomerEmailAfterSave implements ObserverInterface
{
    public const ORIG_CUSTOMER_DATA_OBJECT = 'orig_customer_data_object';
    public const CUSTOMER_DATA_OBJECT = 'customer_data_object';
    private AdapterInterface $connection;

    public function __construct(
        private StoreManagerInterface $storeManager,
        private ProgramScopeConfig $programScopeConfig,
        private LoggerInterface $logger,
        private ResourceConnection $resourceConnection
    ) {}

    public function execute(Observer $observer)
    {
        try {
            if ($this->programScopeConfig->isEnabled((int)$this->storeManager->getWebsite()->getId())) {
                $this->processCustomer($observer);
            }
        } catch (\Exception $exception) {
            $this->logger->error(__($exception->getMessage()));
        }
    }

    /**
     * @param Observer $observer
     * @throws \Exception
     */
    private function processCustomer(Observer $observer): void
    {
        $origCustomerDataObject = $this->getCustomer($observer, self::ORIG_CUSTOMER_DATA_OBJECT);
        if (!$origCustomerDataObject) {
            return;
        }

        $updatedCustomerDataObject = $this->getCustomer($observer, self::CUSTOMER_DATA_OBJECT);
        if (
            $this->isProgramAssignedToCustomer($updatedCustomerDataObject) &&
            $origCustomerDataObject->getEmail() !== $updatedCustomerDataObject->getEmail()
        ) {
            $this->updateCustomerEmail($updatedCustomerDataObject);
        }
    }

    private function getCustomer(Observer $observer, string $customerDataObjectType): ?Customer
    {
        return $observer->getData($customerDataObjectType);
    }

    private function isProgramAssignedToCustomer(Customer $customer): bool
    {
        $this->connection = $this->resourceConnection->getConnection();

        $select = $this->connection->select()
            ->from(CustomerProgramConfig::CUSTOMER_PROGRAM_TABLE, CustomerProgramConfig::PROGRAM_ID)
            ->where(CustomerProgramConfig::CUSTOMER_ID . ' = ' . $customer->getId());
        $result = $this->connection->fetchOne($select);

        return !($result === false || $result === null);
    }

    private function updateCustomerEmail(Customer $customer): void
    {
        $result = $this->connection->update(
            CustomerProgramConfig::CUSTOMER_PROGRAM_TABLE,
            [CustomerProgramConfig::CUSTOMER_EMAIL => $customer->getEmail()],
            CustomerProgramConfig::CUSTOMER_ID . ' = ' . $customer->getId()
        );

        if ($result !== 1) {
            throw new \Exception(
                'Data of Field ' . '\'' . CustomerProgramConfig::CUSTOMER_EMAIL . '\'' .
                ' in table : ' . '\'' . CustomerProgramConfig::CUSTOMER_PROGRAM_TABLE . '\'' .
                'was not updated!'
            );
        }
    }
}
