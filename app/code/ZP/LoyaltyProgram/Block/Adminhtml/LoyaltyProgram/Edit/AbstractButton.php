<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Block\Adminhtml\LoyaltyProgram\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use Magento\Framework\App\RequestInterface;

abstract class AbstractButton implements ButtonProviderInterface
{
    protected ?int $programId = null;
    protected string $requestAction;
    protected RequestInterface $request;

    public const BUTTON_DATA = [];

    public function __construct(protected Context $context)
    {
        $this->request = $this->context->getRequest();
        $this->setProgramIdPropertyValue();
        $this->setActionPropertyValue();
    }


    protected function validateButtonWorkConditions(): bool
    {
        return !$this->isEditAction() || $this->validateProgramId();
    }

    protected function isEditAction(): bool
    {
        return $this->requestAction === 'edit';
    }

    protected function validateProgramId(): bool
    {
        return ($this->programId !== BasicProgramsConfig::PROGRAM_MIN && $this->programId !== BasicProgramsConfig::PROGRAM_MAX);
    }

    protected function setProgramIdPropertyValue(): void
    {
        $programId = $this->request->getParam(LoyaltyProgramInterface::PROGRAM_ID);
        if ($programId !== null) {
            $this->programId = (int)$programId;
        }
    }

    protected function setActionPropertyValue(): void
    {
        $this->requestAction = $this->request->getActionName();
    }

    public function getButtonData(): array
    {
        if ($this->validateButtonWorkConditions()) {
            $buttonData = $this->getData();
        } else {
            $buttonData =[];
        }

        return $buttonData;
    }

    abstract protected function getData(): array;
}
