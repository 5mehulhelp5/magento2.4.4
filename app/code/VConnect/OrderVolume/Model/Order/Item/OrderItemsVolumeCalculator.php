<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\Order\Item;

use Magento\Catalog\Model\Product;
use Magento\Sales\Api\Data\OrderItemInterface;
use VConnect\OrderVolume\Model\Product\Config as ProductConfig;

class OrderItemsVolumeCalculator
{
    private array $orderItemsVolumeData = [];

    public function calculate(array $orderItems): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            $this->setCalculationResult((int)$orderItem->getItemId(), $this->getOrderItemVolume($orderItem));
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return float|int
     * @throws \Exception
     */
    private function getOrderItemVolume(OrderItemInterface $orderItem): float|int
    {
        $orderItemQuantity = $orderItem->getQtyOrdered();
        $productVolume = $this->getProductVolume($orderItem);
        if ($this->isFloat($productVolume)) {
            $productVolume = (float)$productVolume;
        } else {
            $productVolume = (int)$productVolume;
        }

        return $this->calculateOrderItemVolume($orderItemQuantity, $productVolume);
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return mixed
     * @throws \Exception
     */
    private function getProductVolume(OrderItemInterface $orderItem)
    {
        /** @var Product $product */
        $product = $orderItem->getProduct();

        /** @var \Magento\Framework\Api\AttributeInterface $customAttributeInterface */
        $customAttributeInterface = $product->getCustomAttribute(ProductConfig::PRODUCT_VOLUME);
        if ($customAttributeInterface === null) {
            throw new \Exception(
                'Product Custom Attribute : \'' . ProductConfig::PRODUCT_VOLUME . '\' doesn\'t exist!'
            );
        }

        return $customAttributeInterface->getValue();
    }

    /**
     * @param int|float $orderItemQuantity
     * @param int|float $productVolume
     * @return int|float
     */
    private function calculateOrderItemVolume(int|float $orderItemQuantity, int|float $productVolume): int|float
    {
        return $orderItemQuantity * $productVolume;
    }

    public function getCalculationResult(): array
    {
        return $this->orderItemsVolumeData;
    }

    private function setCalculationResult(int $orderItemId, int|float $orderItemVolume): void
    {
        $this->orderItemsVolumeData[$orderItemId] = $orderItemVolume;
    }

    /**
     * @param string $data
     * @return bool
     */
    private function isFloat(string $data): bool
    {
        preg_match('/\./', $data, $matches);
        if (isset($matches[0]) && $matches[0] === '.') {
            return true;
        }

        return false;
    }
}

