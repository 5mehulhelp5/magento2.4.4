<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Configs\Program\Scope\Email;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'program_email_settings/general/is_enabled';
    private const XML_PATH_CUSTOMER_PROGRAM_ASSIGNMENT_TEMPLATE = 'program_email_settings/customer/program_assignment_template';

    public function __construct(private ScopeConfigInterface $scopeConfig)
    {}

    /**
     * @param string|int|null $websiteId
     * @return bool
     */
    public function isEnabled(null|string|int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
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
}
