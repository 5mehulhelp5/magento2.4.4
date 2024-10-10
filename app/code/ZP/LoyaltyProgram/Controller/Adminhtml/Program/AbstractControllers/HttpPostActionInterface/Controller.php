<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program\AbstractControllers\HttpPostActionInterface;

use ZP\LoyaltyProgram\Api\Data\RequestHelperInterface;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program\AbstractControllers\BaseController;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use ZP\LoyaltyProgram\Api\Data\ValidatorInterface;

abstract class Controller extends BaseController implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        protected LoyaltyProgramRepositoryInterface $programRepository,
        protected ValidatorInterface $dataValidator,
        protected RequestHelperInterface $requestHelper
    ) {
        parent::__construct($context, $logger);
    }

    abstract protected function addMessages(): void;
}
