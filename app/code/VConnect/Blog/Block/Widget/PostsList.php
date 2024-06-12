<?php
declare(strict_types=1);

namespace VConnect\Blog\Block\Widget;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use VConnect\Blog\Model\Post;
use VConnect\Blog\Model\ResourceModel\Post\CollectionFactory;
use VConnect\Blog\Model\ResourceModel\Post\Collection;
use Magento\Framework\DB\Select;

class PostsList extends Template implements BlockInterface
{
    private const DEFAULT_POSTS_PER_PAGE = 3;

    protected $_template = "widget/posts_list.phtml";

    /**
     * PostsList constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \VConnect\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        private CollectionFactory $collectionFactory,
        private UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return \VConnect\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPosts(): Collection
    {
        return $this->getPostsCollection();
    }

    public function getPostUrl(Post $post): string
    {
        return $this->url->getBaseUrl() . 'vconnect_blog/post/view/id/' . $post->getData('entity_id');
    }

    /**
     * @return \VConnect\Blog\Model\ResourceModel\Post\Collection
     */
    private function getPostsCollection(): Collection
    {
        /** @var \VConnect\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('publish', '1');
        $collection->setOrder('publish_date', Select::SQL_DESC);
        $postsLimit = $this->getPostsPageSize();
        $collection->setPageSize($postsLimit);

        return $collection;
    }

    /**
     * Returns how many posts should be displayed
     * @return int
     */
    private function getPostsPerPage(): int
    {
        if (!$this->hasData('posts_per_page')) {
            $this->setData('posts_per_page', self::DEFAULT_POSTS_PER_PAGE);
        }

        return (int)$this->getData('posts_per_page');
    }

    /**
     * Returns how many posts should be displayed on page
     * @return int
     */
    private function getPostsPageSize(): int
    {
        return $this->getPostsPerPage();
    }
}
