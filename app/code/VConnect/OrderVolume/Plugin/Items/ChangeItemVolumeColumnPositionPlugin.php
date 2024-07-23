<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin\Items;

use Magento\Sales\Block\Adminhtml\Order\View\Items;
use VConnect\OrderVolume\Service\Order\View\Items\Columns\Volume\ChangePosition;

class ChangeItemVolumeColumnPositionPlugin
{
    public function __construct(private ChangePosition $changeItemVolumeColumnPosition)
    {}

    /**
     * @param Items $subject
     * @param $result
     * @return array
     */
    public function afterGetColumns(Items $subject, $result): array
    {
        return $this->changeItemVolumeColumnPosition->execute($result, 'ordered-qty');
    }
}
