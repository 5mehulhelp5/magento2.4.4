<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Model\Order\Item;

use Magento\Catalog\Model\Product;
use Magento\Sales\Api\Data\OrderItemInterface;
use VConnect\OrderVolume\Setup\Patch\Data\AddProductEntityVolumeAttribute as ProductConfig;

class OrderItemsVolumeCalculator
{
    private array $orderItemsVolumeData = [];

    /**
     * @param array $orderItems
     */
    public function calculate(array $orderItems): void
    {
        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItemId => $orderItem) {
            $this->setCalculationResult($orderItemId, $this->getOrderItemVolume($orderItem));
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return float|null
     */
    private function getOrderItemVolume(OrderItemInterface $orderItem): ?float
    {
        $orderItemQuantity = $orderItem->getQtyOrdered();
        $productVolume = $this->getProductVolume($orderItem);

        return is_null($orderItemQuantity) || is_null($productVolume) ?
            null : $this->calculateOrderItemVolume($orderItemQuantity, (float)$productVolume);
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return mixed|null
     */
    private function getProductVolume(OrderItemInterface $orderItem)
    {
        /** @var Product $product */
        $product = $orderItem->getProduct();

        /** @var \Magento\Framework\Api\AttributeInterface $customAttributeInterface */
        $customAttributeInterface = $product->getCustomAttribute(ProductConfig::PRODUCT_VOLUME);

        return $customAttributeInterface === null ? null : $customAttributeInterface->getValue();
    }

    /**
     * @param int|float $orderItemQuantity
     * @param float $productVolume
     * @return float
     */
    private function calculateOrderItemVolume(int|float $orderItemQuantity, float $productVolume): float
    {
        return (float)($orderItemQuantity * $productVolume);
    }

    public function getCalculationResult(): array
    {
        return $this->orderItemsVolumeData;
    }

    /**
     * @param int $orderItemId
     * @param float|null $orderItemVolume
     */
    private function setCalculationResult(int $orderItemId, ?float $orderItemVolume): void
    {
        $this->orderItemsVolumeData[$orderItemId] = $orderItemVolume;
    }
}

