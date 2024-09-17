<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Controller\Adminhtml\Program;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram as LoyaltyProgramResource;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory;
use Magento\Backend\App\Action;

class MassDelete extends Action implements HttpPostActionInterface
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
        $deletedPrograms = $collection->getSize();


        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            /** @var LoyaltyProgram $program */
            foreach ($collection as $program) {
                $this->programResource->delete($program);
            }

            $remainingProgramsCollection = $this->collectionFactory->create();
            $remainingProgramsCollection->addFieldToFilter('program_id', $remainingProgramsCollection->getNinBasicProgramsFilter());
            $remainingProgramsSize = $remainingProgramsCollection->getSize();
            if ($remainingProgramsSize === 0) {
                $this->messageManager->addNoticeMessage(__('You deleted all Programs! Probably you should create new!'));
            } else {
                $this->messageManager->addNoticeMessage(
                    __(
                        'A total of %1 program(s) have been deleted, ' .
                        'don\'t forget to check and update (if it is need) reference programs chain in other programs!',
                        $deletedPrograms
                    )
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
