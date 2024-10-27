<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Configs\Program\Scope\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const GENERAL_GROUP_CONFIG_TYPE = 'general';
    public const CUSTOMER_GROUP_CONFIG_TYPE = 'customer';
    private const XML_PATH = 'program_email_settings/';
    private const XML_PATH_ENABLED = self::XML_PATH . 'general/is_enabled';
    private const XML_PATH_CUSTOMER = self::XML_PATH . 'customer/';
    private const XML_PATH_IS_EMAIL_ENABLE_TO_SEND_TO_CUSTOMER = self::XML_PATH_CUSTOMER . 'is_email_enable_to_send_to_customer';
    private const XML_PATH_CUSTOMER_PROGRAM_ASSIGNMENT_TEMPLATE = self::XML_PATH_CUSTOMER . 'program_assignment_template';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * @param string $groupConfigType
     * @param string|int|null $websiteId
     * @return bool
     * @throws \Exception
     */
    public function isEnabled(string $groupConfigType, null|string|int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            $this->getIsEnableGroupConfigPath($groupConfigType),
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCustomerProgramAssignmentTemplate(): int
    {
        $templateId = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_PROGRAM_ASSIGNMENT_TEMPLATE,
            ScopeInterface::SCOPE_WEBSITE
        );

        return $templateId !== null ? (int)$templateId : throw new \Exception('NULL Template Id!');
    }

    /**
     * @param string $groupConfigType
     * @return string
     * @throws \Exception
     */
    private function getIsEnableGroupConfigPath(string $groupConfigType): string
    {
        return match ($groupConfigType) {
            self::GENERAL_GROUP_CONFIG_TYPE => self::XML_PATH_ENABLED,
            self::CUSTOMER_GROUP_CONFIG_TYPE => self::XML_PATH_IS_EMAIL_ENABLE_TO_SEND_TO_CUSTOMER,
            default => throw new \Exception('Unknown group config type \'' . $groupConfigType . '\'!')
        };
    }
}
