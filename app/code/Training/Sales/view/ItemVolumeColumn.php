<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Block\Adminhtml\Order\Item;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;
use VConnect\OrderVolume\Model\ResourceModel\Order\Item\Volume\GetOrderItemVolume;

class ItemVolumeColumn extends DefaultColumn
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        private GetOrderItemVolume $getOrderItemVolume,
        array $data = []
    ) {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

//    public function getItemVolume($item): float|int
//    {
//        return $item->getItemVolume() ?? 0;
//    }
//
//    public function getItem()
//    {
//        return $this->getParentBlock()->getItem();
//    }
}
