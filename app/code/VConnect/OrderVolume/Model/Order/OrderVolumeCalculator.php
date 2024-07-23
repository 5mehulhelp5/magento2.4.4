<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\Order;

class OrderVolumeCalculator
{
    private int|float $orderTotalVolume = 0;

    public function calculate(array $orderItemsVolumeData): void
    {
        foreach ($orderItemsVolumeData as $orderItemVolumeData) {
            $this->orderTotalVolume += $orderItemVolumeData;
        }
    }

    public function getCalculationResult(): int|float
    {
        return $this->orderTotalVolume;
    }
}
