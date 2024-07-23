<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Plugin\Items;

use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;
use VConnect\OrderVolume\Service\Order\View\Items\Columns\Volume\ChangePosition;

class ChangeItemVolumeColumnDataPositionPlugin
{
    public function __construct(private ChangePosition $changeItemVolumeColumnDataPosition)
    {}
    /**
     * @param DefaultRenderer $subject
     * @param $result
     * @return array
     */
    public function afterGetColumns(DefaultRenderer $subject, $result): array
    {
        return $this->changeItemVolumeColumnDataPosition->execute($result, 'qty');
    }
}
