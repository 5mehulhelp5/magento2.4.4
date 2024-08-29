<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Block\Adminhtml\LoyaltyProgram\Edit;

class SaveAndContinueButton extends AbstractButton
{
    protected function getData(): array
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save primary',
            'on_click' => '',
            'sort_order' => 20,
        ];
    }
}
