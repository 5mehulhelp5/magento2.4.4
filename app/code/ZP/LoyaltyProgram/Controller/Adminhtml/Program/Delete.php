<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program as AbstractProgramController;

class Delete extends AbstractProgramController implements HttpPostActionInterface
{
    /**
     * Save constructor.
     * @param Context $context
     * @param LoyaltyProgramRepositoryInterface $programRepository
     */
    public function __construct(
        Context $context,
        private LoyaltyProgramRepositoryInterface $programRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $programId = $this->getRequest()->getParam(LoyaltyProgramInterface::PROGRAM_ID);

        try {
            $program = $this->programRepository->get((int)$programId);
            $programName = $program->getProgramName();
            $this->programRepository->delete($program);
            $this->messageManager->addNoticeMessage(
                'You have deleted ' . "'$programName" . ' Program\', ' .
                'don\'t forget to check and update (if it is need) reference programs chain in other programs!'
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
            return $resultRedirect->setPath('*/*/edit', ['program_id' => (int)$programId]);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
