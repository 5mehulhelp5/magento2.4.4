<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use ZP\LoyaltyProgram\Model\Controller\Adminhtml\Program\RequestHelper;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program\AbstractControllers\HttpPostActionInterface\SaveAndDelete\Controller;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use Psr\Log\LoggerInterface;
use ZP\LoyaltyProgram\Api\Model\Validators\Controller\Adminhtml\Program\ValidatorInterface;

class Delete extends Controller
{
    public const BASIC_PROGRAM_ERR = ' DELETE ';

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LoyaltyProgramRepositoryInterface $programRepository,
        ValidatorInterface $dataValidator,
        RequestHelper $requestHelper
    ) {
        parent::__construct(
            $context,
            $logger,
            $programRepository,
            $dataValidator,
            $requestHelper
        );
    }

    public function execute()
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $programId = $this->requestHelper->getProgramIdFromRequest($this->getRequest());
            $this->validateProgramId($programId);
            $this->isBasicProgram();
            $program = $this->programRepository->get($this->programId);
            $this->programName = $program->getProgramName();
            $this->programRepository->delete($program);
            $this->addMessages();
        } catch (NoSuchEntityException) {
            $this->messageManager->addNoticeMessage(__('Such program does not exist already!'));
            $this->logger->notice(
                __('Someone tried to delete program that already does not exist or didn\'t exist at all!')
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
            $this->logger->notice(__($exception->getMessage()));
            return $resultRedirect->setPath('*/*/edit', ['program_id' => $this->programId]);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Sorry, something went wrong while trying to delete program!'));
            $this->logger->notice(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @throws \Exception
     */
    protected function nullProgramIdReaction(): void
    {
        throw new \Exception('No \'' . LoyaltyProgram::PROGRAM_ID . '\' data from request string!');
    }

    protected function addMessages(): void
    {
        $this->messageManager->addSuccessMessage(
            'You have successfully deleted' . ' \'' . $this->programName . '\' Program!'
        );
    }
}
