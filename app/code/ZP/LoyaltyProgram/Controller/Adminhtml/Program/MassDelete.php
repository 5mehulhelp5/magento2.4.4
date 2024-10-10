<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use ZP\LoyaltyProgram\Model\Controller\Adminhtml\Program\MassAction\RequestHelper;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\Collection;
use ZP\LoyaltyProgram\Controller\Adminhtml\Program\AbstractControllers\HttpPostActionInterface\MassAction\Controller;
use ZP\LoyaltyProgram\Model\Prepare\Data\DataPreparer;
use ZP\LoyaltyProgram\Api\LoyaltyProgramRepositoryInterface;
use ZP\LoyaltyProgram\Api\Model\Validators\Controller\Adminhtml\Program\MassAction\ValidatorInterface;

class MassDelete extends Controller
{
    private const DELETE = 'delete';
    private int $deletedProgramsCount = 0;
    private int $deletingProgramsErrorCount = 0;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LoyaltyProgramRepositoryInterface $programRepository,
        ValidatorInterface $dataValidator,
        RequestHelper $requestHelper,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DataPreparer $prepareData
    ) {
        parent::__construct(
            $context,
            $logger,
            $programRepository,
            $dataValidator,
            $requestHelper,
            $filter,
            $collectionFactory,
            $prepareData
        );
    }

    public function execute()
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->checkProgramIds(self::DELETE);

            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            if (!$collection->getSize()) {
                $this->messageManager->addNoticeMessage(
                    __('Program(s) with specified ids don\'t exist.')
                );
            } else {
                $this->validateCollectionPrograms($collection);
                if (count($collection->getItems()) !== 0) {
                    $this->deletePrograms($collection->getItems());
                }

                $this->addMessages();
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('Sorry something went wrong while trying to delete program(s)!')
            );
            $this->logger->notice(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param LoyaltyProgram[] $programs
     */
    public function deletePrograms(array $programs): void
    {
        /**
         * @var int $programId
         * @var LoyaltyProgram $program
         */
        foreach ($programs as $program) {
            try {
                $this->programRepository->delete($program);
                $this->deletedProgramsCount++;
            } catch (\Exception $exception) {
                $this->logger->error(__($exception->getMessage()));
                $this->deletingProgramsErrorCount++;
            }
        }
    }

    protected function addMessages(): void
    {
        if ($this->deletedProgramsCount) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 program(s) has(have) been deleted.', $this->deletedProgramsCount)
            );
        } else {
            $this->messageManager->addErrorMessage(
                __(
                    'Something went wrong during deleting of %1 selected program(s)!',
                    $this->deletingProgramsErrorCount
                )
            );
        }

        if ($this->basicProgramMsgStatus) {
            $this->messageManager->addNoticeMessage(__('You are not allowed to delete BASIC PROGRAMS!'));
        }
    }
}
