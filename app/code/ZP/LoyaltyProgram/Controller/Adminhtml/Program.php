<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

abstract class Program extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'ZP_LoyaltyProgram::manage';

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        return $resultPage;
    }
}
