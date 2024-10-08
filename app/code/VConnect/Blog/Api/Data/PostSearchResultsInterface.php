<?php
declare(strict_types=1);

namespace VConnect\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get posts
     *
     * @return \VConnect\Blog\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * Set posts
     *
     * @param \VConnect\Blog\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

