<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Source\Adminhtml\SalesRule\Form\Fields\Field;

use Magento\Framework\Data\OptionSourceInterface;

class ApplyActionOptions implements OptionSourceInterface
{
    public const NONE_ACTION_VALUE = 0;
    public const PERCENTAGE_OF_PRODUCT_ACTION_VALUE = 1;
    public const FIX_AMOUNT_ALL_CART_ACTION_VALUE = 2;

    public function toOptionArray()
    {
        return [
            [
                'label' => __('None'),
                'value' => self::NONE_ACTION_VALUE
            ],
            [
                'label' => __('Percentage of product'),
                'value' => self::PERCENTAGE_OF_PRODUCT_ACTION_VALUE
            ],
            [
                'label' => __('Fix amount all cart'),
                'value' => self::FIX_AMOUNT_ALL_CART_ACTION_VALUE
            ]
        ];
    }
}
