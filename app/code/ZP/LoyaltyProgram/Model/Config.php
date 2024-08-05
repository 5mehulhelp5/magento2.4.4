<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'loyalty_program/general/is_enabled';

    public function __construct(private ScopeConfigInterface $scopeConfig)
    {}

    /**
     * @param string|int|null $storeId
     * @return bool
     */
    public function isEnabled(null|string|int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

}
