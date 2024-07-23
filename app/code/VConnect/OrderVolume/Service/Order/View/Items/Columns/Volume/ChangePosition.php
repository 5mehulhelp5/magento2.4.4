<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Service\Order\View\Items\Columns\Volume;

use VConnect\OrderVolume\Model\Order\Item\Config;

class ChangePosition
{
    private array $columnsCurrentPositionNumber = [];

    public function execute(array $columns, string $afterColumnName): array
    {
        $this->defineColumnsCurrentPositionNumbers($columns);
        $itemVolumeColumnCurrentPosition = $this->getColumnCurrentPositionNumber(Config::ITEM_VOLUME_COLUMN);
        $beforeItemVolumeColumnsNumber = $this->getColumnCurrentPositionNumber($afterColumnName);
        $beforeItemVolumeColumns = $this->arraySplice($columns, 0, $beforeItemVolumeColumnsNumber);
        $itemVolumeColumn = $this->arraySplice($columns, $itemVolumeColumnCurrentPosition - 1, 1);

        return array_merge($beforeItemVolumeColumns,$itemVolumeColumn,$columns);
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
        $columnPositionNumber = 0;
        if (array_key_exists($columnName, $this->columnsCurrentPositionNumber)) {
            $columnPositionNumber = $this->columnsCurrentPositionNumber[$columnName];
        }

        return $columnPositionNumber;
    }

    private function arraySplice(array $array, int $offset, int $length): array
    {
        return array_splice($array, $offset, $length);
    }
}
