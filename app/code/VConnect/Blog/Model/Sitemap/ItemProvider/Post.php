<?php
declare(strict_types=1);

namespace VConnect\Blog\Model\Sitemap\ItemProvider;

use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Magento\Sitemap\Model\ItemProvider\ConfigReaderInterface;
use VConnect\Blog\Model\ResourceModel\Sitemap\PostFactory;

class Post implements ItemProviderInterface
{
    /**
     * Post constructor.
     * @param \Magento\Sitemap\Model\ItemProvider\ConfigReaderInterface $configReader
     * @param \VConnect\Blog\Model\ResourceModel\Sitemap\PostFactory $postFactory
     * @param \Magento\Sitemap\Model\SitemapItemInterfaceFactory $itemFactory
     */
    public function __construct(
        private ConfigReaderInterface $configReader,
        private PostFactory $postFactory,
        private SitemapItemInterfaceFactory $itemFactory
    ) {}

    /**
     * @param int $storeId
     * @return array|\Magento\Sitemap\Model\SitemapItemInterface[]
     * @throws \Exception
     */
    public function getItems($storeId): array
    {
        $collection = $this->postFactory->create()->getCollection();
        $items = array_map(function ($item) use ($storeId) {
            return $this->itemFactory->create([
                'url' => $item->getUrl(),
                'updatedAt' => $item->getUpdatedAt(),
                'priority' => $this->configReader->getPriority($storeId),
                'changeFrequency' => $this->configReader->getChangeFrequency($storeId),
            ]);
        }, $collection);

        return $items;
    }
}
