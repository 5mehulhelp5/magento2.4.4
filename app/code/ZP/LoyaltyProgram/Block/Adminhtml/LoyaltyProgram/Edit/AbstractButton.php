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
    protected RequestInterface $request;

    public function __construct(protected Context $context)
    {
        $this->request = $this->context->getRequest();
        $this->setProgramIdPropertyValue();
    }


    protected function validateButtonWorkConditions(): bool
    {
        return !$this->isEditAction() || $this->validateProgramId();
    }

    protected function isEditAction(): bool
    {
        return $this->request->getActionName() === 'edit';
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

    public function getButtonData(): array
    {
        $buttonData =[];
        if ($this->validateButtonWorkConditions()) {
            $buttonData = $this->getData();
        }

        return $buttonData;
    }

    abstract protected function getData(): array;
}
