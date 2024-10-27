<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Customer\Email;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use ZP\LoyaltyProgram\Model\Configs\Program\Scope\Email\Config;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use Magento\Framework\App\Area;

class Manager
{
    private const SENDER_EMAIL = 'mailhog@gmail.com';
    private const SENDER_NAME = 'MailHog';

    public function __construct(
        private Config $emailConfig,
        private TransportBuilder $transportBuilder,
        private StoreManagerInterface $storeManager,
    ) {}

    /**
     * @param CustomerInterface $customer
     * @param LoyaltyProgram $loyaltyProgram
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function sendProgramAssignmentEmailMsg(CustomerInterface $customer, LoyaltyProgram $loyaltyProgram): void
    {
        if ($this->isEnabledToSendEmailToCustomer((int)$customer->getWebsiteId())) {
            $this->sendEmailMessage($customer, $loyaltyProgram);
        }
    }

    /**
     * @param int $websiteId
     * @return bool
     * @throws \Exception
     */
    private function isEnabledToSendEmailToCustomer(int $websiteId): bool
    {
        return $this->emailConfig->isEnabled(Config::GENERAL_GROUP_CONFIG_TYPE, $websiteId) &&
            $this->emailConfig->isEnabled(Config::CUSTOMER_GROUP_CONFIG_TYPE, $websiteId);
    }

    /**
     * @param CustomerInterface $customer
     * @param LoyaltyProgram $loyaltyProgram
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    private function sendEmailMessage(CustomerInterface $customer, LoyaltyProgram $loyaltyProgram)
    {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->emailConfig->getCustomerProgramAssignmentTemplate())
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId()
                ]
            )->setTemplateVars(
                [
                    'customerName' => $customer->getFirstname(),
                    'programName' => $loyaltyProgram->getProgramName()
                ]
            )->setFrom(
                [
                    'name' => self::SENDER_NAME,
                    'email' => self::SENDER_EMAIL
                ]
            )->addTo($customer->getEmail())
            ->getTransport();

        $transport->sendMessage();
    }
}
