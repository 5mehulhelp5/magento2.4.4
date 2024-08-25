<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\LoyaltyProgramFactory;
use Magento\Framework\Exception\LocalizedException;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program as AbstractProgramController;
use Magento\Framework\Controller\ResultInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class Save extends AbstractProgramController implements HttpPostActionInterface
{
    /**
     * Save constructor.
     * @param Context $context
     * @param LoyaltyProgramFactory $programFactory
     * @param LoyaltyProgramRepositoryInterface $programRepository
     */
    public function __construct(
        Context $context,
        private LoyaltyProgramFactory $programFactory,
        private LoyaltyProgramRepositoryInterface $programRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $programId = $this->getRequest()->getParam(LoyaltyProgram::PROGRAM_ID);
            if (empty($data[LoyaltyProgram::PROGRAM_ID])) {
                $data[LoyaltyProgram::PROGRAM_ID] = null;
            } else {
                $programId = (int)$data[LoyaltyProgram::PROGRAM_ID];
                $data[LoyaltyProgram::PROGRAM_ID] = $programId;
            }

            if (!$this->checkCorrectProgramId($data[LoyaltyProgram::PROGRAM_ID], $programId)) {
                $this->messageManager->addErrorMessage('You can\'t edit Basic Programs.');

                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->prepareDataToSave($data);

            /** @var LoyaltyProgram $program */
            $program = $this->programFactory->create();

            if ($programId && (is_numeric($programId) && !is_float($programId))) {
                try {
                    $program = $this->programRepository->get((int)$programId);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This Program no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
            }

            $program->setData($data);

            try {
                $this->programRepository->save($program);
                $this->messageManager->addSuccessMessage(__('You saved the Program.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/new');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Program.'));
            }

            if ($programId === null) {
                $programId = $program->getId();
            }

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', [LoyaltyProgram::PROGRAM_ID => (int)$programId]);
            }

            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function prepareDataToSave(array $data): array
    {
        if (empty($data[LoyaltyProgram::PROGRAM_ID])) {
            $data[LoyaltyProgram::PROGRAM_ID] = null;
        } else {
            $programId = (int)$data[LoyaltyProgram::PROGRAM_ID];
            $data[LoyaltyProgram::PROGRAM_ID] = $programId;
        }

        if (isset($data[LoyaltyProgram::IS_ACTIVE]) && $data[LoyaltyProgram::IS_ACTIVE] === '1') {
            $data[LoyaltyProgram::IS_ACTIVE] = LoyaltyProgram::ACTIVE;
        }

        if (isset($data[LoyaltyProgram::IS_PROGRAM_MINIMUM])) {
            if ($data[LoyaltyProgram::IS_PROGRAM_MINIMUM] === '1') {
                $data[LoyaltyProgram::PREVIOUS_PROGRAM] = BasicProgramsConfig::PROGRAM_MIN;
            } else {
                if ($data[LoyaltyProgram::PREVIOUS_PROGRAM] === '0') {
                    $data[LoyaltyProgram::PREVIOUS_PROGRAM] = null;
                }
            }

            unset($data[LoyaltyProgram::IS_PROGRAM_MINIMUM]);
        }

        if (isset($data[LoyaltyProgram::IS_PROGRAM_MAXIMUM])) {
            if ($data[LoyaltyProgram::IS_PROGRAM_MAXIMUM] === '1') {
                $data[LoyaltyProgram::NEXT_PROGRAM] = BasicProgramsConfig::PROGRAM_MAX;
            } else {
                if ($data[LoyaltyProgram::NEXT_PROGRAM] === '0') {
                    $data[LoyaltyProgram::NEXT_PROGRAM] = null;
                }
            }

            unset($data[LoyaltyProgram::IS_PROGRAM_MAXIMUM]);
        }

        if (!isset($data[LoyaltyProgram::WEBSITE_ID]) || empty($data[LoyaltyProgram::WEBSITE_ID]) ||
                $data[LoyaltyProgram::WEBSITE_ID] === '0'
        ) {
            $data[LoyaltyProgram::WEBSITE_ID] = null;
        }

        if (!isset($data[LoyaltyProgram::CUSTOMER_GROUP_IDS]) || empty($data[LoyaltyProgram::CUSTOMER_GROUP_IDS])) {
            $data[LoyaltyProgram::CUSTOMER_GROUP_IDS] = null;
        } else {
            $data[LoyaltyProgram::CUSTOMER_GROUP_IDS] = implode(',', $data[LoyaltyProgram::CUSTOMER_GROUP_IDS]);
        }

        if (!isset($data[LoyaltyProgram::ORDER_SUBTOTAL]) || empty($data[LoyaltyProgram::ORDER_SUBTOTAL])) {
            $data[LoyaltyProgram::ORDER_SUBTOTAL] = null;
        }

        return $data;
    }

    public function checkCorrectProgramId($postDataProgramId, $programIdFromRequest): bool
    {
        if (
            $this->isBasicProgram((int)$programIdFromRequest) ||
            (!empty($postDataProgramId) && $this->isBasicProgram((int)$postDataProgramId))
        ) {
            return false;
        }

        return true;
    }

    private function isBasicProgram(int $programId): bool
    {
        return $programId === BasicProgramsConfig::PROGRAM_MIN || $programId === BasicProgramsConfig::PROGRAM_MAX;
    }
}
