<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Block\Adminhtml\LoyaltyProgram\Edit;

class DeleteButton extends AbstractButton
{
    private function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    protected function validateProgramId(): bool
    {
        return (parent::validateProgramId() && $this->programId !== null);
    }

    protected function getData(): array
    {
        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this?'
                ) . '\', \'' . $this->getUrl('*/*/delete', ['program_id' => $this->programId]) . '\', {data: {}})',
            'sort_order' => 100,
        ];
    }
}
