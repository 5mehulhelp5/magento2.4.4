<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResource;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory;

class MassStatus extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'ZP_LoyaltyProgram::manage';

    public function __construct(
        Context $context,
        private Filter $filter,
        private CollectionFactory $collectionFactory,
        private LoyaltyProgramResource $programResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $programStatusesChanged = 0;
        $status = (int) $this->getRequest()->getParam('status');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            /** @var LoyaltyProgram $program */
            foreach ($collection as $program) {
                if (!$this->isSameStatus((bool)$status, $program)) {
                    $program->setIsActive((bool)$status);
                    $this->programResource->save($program);
                    $programStatusesChanged++;
                }
            }

            if ($programStatusesChanged === 0 ) {
                $this->messageManager->addNoticeMessage('You didn\'t change status for any program.');
            } else {
                $this->messageManager->addNoticeMessage(
                    __(
                        'A total of %1 program(s) have been changed status.' .
                        'don\'t forget to check and update (if it is need) reference programs chain in other programs!',
                        $programStatusesChanged
                    )
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }

    private function isSameStatus(bool $status, LoyaltyProgram $program): bool
    {
        return $status === $program->getIsActive();
    }
}
