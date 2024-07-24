<?php
declare(strict_types=1);

namespace VConnect\OrderVolume\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class AddProductEntityVolumeAttribute implements DataPatchInterface
{
    public const PRODUCT_VOLUME = 'volume';

    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavSetupFactory $eavSetupFactory
    ) {}

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::PRODUCT_VOLUME,
            [
                'group' => 'General',
                'type' => 'decimal',
                'label' => 'Product Volume',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 100,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'unique' => false,
                'apply_to' => 'simple,configurable,bundle,grouped'
            ]
        );
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
