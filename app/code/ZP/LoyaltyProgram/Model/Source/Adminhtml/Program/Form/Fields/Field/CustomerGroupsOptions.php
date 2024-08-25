<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Source\Adminhtml\Program\Form\Fields\Field;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;

class CustomerGroupsOptions implements OptionSourceInterface
{
    public function __construct(private CustomerGroupCollectionFactory $customerGroupFactory)
    {}

    public function toOptionArray()
    {
        return $this->getData();
    }

    protected function getData(): array
    {
        $data = [];

        /** @var CustomerGroupCollection $collection */
        $collection = $this->customerGroupFactory->create();

        /** @var Group $customerGroup */
        foreach ($collection->getItems() as $customerGroup) {
            $data[] = ['label' => __($customerGroup->getCode()), 'value' => $customerGroup->getId()];
        }

        return $data;
    }
}
