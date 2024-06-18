<?php
declare(strict_types=1);

namespace VConnect\Blog\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use VConnect\Blog\Model\Post;
use VConnect\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\DB\Select;

class PostsList extends Template implements BlockInterface, IdentityInterface
{
    private const DEFAULT_POSTS_PER_PAGE = 3;

    protected $_template = "widget/posts_list.phtml";

    /**
     * PostsList constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \VConnect\Blog\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        private CollectionFactory $collectionFactory,
        private UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getPosts(): array
    {
        /** @var \VConnect\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('publish', '1');
        $collection->setOrder('publish_date', Select::SQL_DESC);

        return $collection->setPageSize($this->getPostsPageSize())->getItems();
    }

    public function getPostUrl(Post $post): string
    {
        $postUrlKey = $post->getUrlKey();
        if ($postUrlKey !== null && !empty($postUrlKey)) {
            $postUrl = $this->urlBuilder->getBaseUrl() . 'blog/' . $postUrlKey;
        } else {
            $postUrl = $this->urlBuilder->getUrl(
                'vconnect_blog/post/view',
                ['id' => $post->getData('entity_id'), '_secure' => true]
            );
        }

        return $postUrl;
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

    public function getIdentities(): array
    {
        $identities = [];
        if ($this->getPostsCollection()) {
            foreach ($this->getPostsCollection() as $post) {
                if ($post instanceof IdentityInterface) {
                    $identities[] = $post->getIdentities();
                }
            }
        }

        $identities = array_merge([], ...$identities);

        return $identities;
    }
}
