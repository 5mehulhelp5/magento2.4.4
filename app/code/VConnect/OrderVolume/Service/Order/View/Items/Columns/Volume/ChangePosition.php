<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Service\Order\View\Items\Columns\Volume;

use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\GetOrderItemVolume as OrderItemConfig;

class ChangePosition
{
    private array $columnsCurrentPositionNumber = [];

    public function __construct(private string $afterColumnName = '')
    {
    }

    public function execute(array $columns): array
    {
        $this->defineColumnsCurrentPositionNumbers($columns);
        $itemVolumeColumnCurrentPosition = $this->getColumnCurrentPositionNumber(OrderItemConfig::ITEM_VOLUME);
        $beforeItemVolumeColumnsNumber = $this->getColumnCurrentPositionNumber($this->afterColumnName);
        if ($itemVolumeColumnCurrentPosition > 0 && $beforeItemVolumeColumnsNumber >0) {
            $itemVolumeColumn = array_splice($columns, $itemVolumeColumnCurrentPosition - 1, 1);
            $beforeItemVolumeColumns = array_splice($columns, 0, $beforeItemVolumeColumnsNumber);

            return array_merge($beforeItemVolumeColumns,$itemVolumeColumn,$columns);
        }

        return $columns;
    }

    private function defineColumnsCurrentPositionNumbers(array $columns): void
    {
        $i = 1;
        foreach ($columns as $columnName => $columnTitle) {
            $this->columnsCurrentPositionNumber[$columnName] = $i++;
        }
    }

    /**
     * @param string $columnName
     * @return int
     */
    private function getColumnCurrentPositionNumber(string $columnName): int
    {
        return $this->columnsCurrentPositionNumber[$columnName] ?? -1;
    }
}
