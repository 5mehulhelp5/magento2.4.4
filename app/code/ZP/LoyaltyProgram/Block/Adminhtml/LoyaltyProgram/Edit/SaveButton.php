<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Block\Adminhtml\LoyaltyProgram\Edit;

class SaveButton extends AbstractButton
{
    protected function getData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => '',
            'sort_order' => 10
        ];
    }
}
