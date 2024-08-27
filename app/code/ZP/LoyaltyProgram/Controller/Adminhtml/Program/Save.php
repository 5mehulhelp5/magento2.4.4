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
    private ?int $programId;

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
            $requestProgramId = $this->getRequest()->getParam(LoyaltyProgram::PROGRAM_ID);
            if (empty($data[LoyaltyProgram::PROGRAM_ID])) {
                $data[LoyaltyProgram::PROGRAM_ID] = null;
            }

            if (!$this->checkCorrectProgramId($requestProgramId, $data[LoyaltyProgram::PROGRAM_ID])) {
                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->prepareDataToSave($data);

            /** @var LoyaltyProgram $program */
            $program = $this->programFactory->create();

            if ($this->programId && (is_numeric($this->programId) && !is_float($this->programId))) {
                try {
                    $program = $this->programRepository->get($this->programId);
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

            if ($this->programId === null) {
                $this->programId = (int)$program->getId();
            }

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', [LoyaltyProgram::PROGRAM_ID => $this->programId]);
            }

            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function prepareDataToSave(array $data): array
    {
        if (isset($data[LoyaltyProgram::IS_ACTIVE]) && $data[LoyaltyProgram::IS_ACTIVE] === '1') {
            $data[LoyaltyProgram::IS_ACTIVE] = LoyaltyProgram::ACTIVE;
        }

        $this->processProgramData(
            $data,
            LoyaltyProgram::IS_PROGRAM_MINIMUM,
            BasicProgramsConfig::PROGRAM_MIN,
            LoyaltyProgram::PREVIOUS_PROGRAM
        );
        $this->processProgramData(
            $data,
            LoyaltyProgram::IS_PROGRAM_MAXIMUM,
            BasicProgramsConfig::PROGRAM_MAX,
            LoyaltyProgram::NEXT_PROGRAM
        );
        $this->processField($data, LoyaltyProgram::WEBSITE_ID);
        $this->processField($data, LoyaltyProgram::CUSTOMER_GROUP_IDS, true);
        $this->processField($data, LoyaltyProgram::ORDER_SUBTOTAL);

        return $data;
    }

    public function checkCorrectProgramId(?string $requestProgramId, ?string $postProgramId): bool
    {
        if ($requestProgramId !== $postProgramId) {
            $this->messageManager->addErrorMessage('Different program ids from request and post data!');

            return false;
        }

        if (
            $this->isBasicProgram((int)$requestProgramId) ||
            (!empty($postProgramId) && $this->isBasicProgram((int)$postProgramId))
        ) {
            $this->messageManager->addErrorMessage('You can\'t edit Basic Programs.');

            return false;
        }

        $this->programId = $postProgramId === null ? null : (int)$postProgramId;

        return true;
    }

    private function isBasicProgram(int $programId): bool
    {
        return $programId === BasicProgramsConfig::PROGRAM_MIN || $programId === BasicProgramsConfig::PROGRAM_MAX;
    }

    private function processProgramData(array &$data, string $programKey, int $programConfig, string $programNextOrPrevious): void
    {
        if (isset($data[$programKey])) {
            if ($data[$programKey] === '1') {
                $data[$programNextOrPrevious] = $programConfig;
            } elseif ($data[$programNextOrPrevious] === '0') {
                $data[$programNextOrPrevious] = null;
            }

            unset($data[$programKey]);
        }
    }

    private function processField(array &$data, string $field, bool $implode = false): void
    {
        if (!isset($data[$field]) || empty($data[$field]) || $data[$field] === '0') {
            $data[$field] = null;
        } elseif ($implode) {
            $data[$field] = implode(',', $data[$field]);
        }
    }
}
