<?php
declare(strict_types=1);

namespace VConnect\Blog\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Model\ResourceModel\Post as PostResourceModel;
use Magento\Framework\UrlInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Post extends AbstractModel implements PostInterface, IdentityInterface
{
    /**
     * Post's publish statuses
     */
    public const PUBLISHED = 1;
    public const NOT_PUBLISHED = 0;

    /**
     * Post cache tag
     */
    public const CACHE_TAG = 'vconnect_blog_post';

    /**
     * Post constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        private UrlInterface $urlBuilder,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PostResourceModel::class);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $title
     * @return PostInterface
     */
    public function setTitle(string $title): PostInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @param string $content
     * @return PostInterface
     */
    public function setContent(string $content): PostInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @return string|null
     */
    public function getAnnounce(): ?string
    {
        return $this->getData(self::ANNOUNCE);
    }

    /**
     * @param string $announce
     * @return PostInterface
     */
    public function setAnnounce(string $announce): PostInterface
    {
        return $this->setData(self::ANNOUNCE, $announce);
    }

    /**
     * @return string|null
     */
    public function getPublishDate(): ?string
    {
        return $this->getData(self::PUBLISH_DATE);
    }

    /**
     * @param string|null $publishDate
     * @return PostInterface
     */
    public function setPublishDate(?string $publishDate = null): PostInterface
    {
        return $this->setData(self::PUBLISH_DATE, $publishDate);
    }

    /**
     * @return bool
     */
    public function getPublish(): bool
    {
        return (bool)$this->getData(self::PUBLISH);
    }

    /**
     * @param bool $publish
     * @return PostInterface
     */
    public function setPublish(?bool $publish = null): PostInterface
    {
        return $this->setData(self::PUBLISH, $publish);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string|null $createdAt
     * @return PostInterface
     */
    public function setCreatedAt(?string $createdAt = null): PostInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string|null $updatedAt
     * @return PostInterface
     */
    public function setUpdatedAt(?string $updatedAt = null): PostInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @return string|null
     */
    public function getUrlKey(): ?string
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @param string|null $urlKey
     * @return PostInterface
     */
    public function setUrlKey(?string $urlKey = null): PostInterface
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Returns url path to post page
     *
     * @return string
     */
    public function getUrl(): string
    {
        $postUrlKey = $this->getUrlKey();
        if (!empty($postUrlKey)) {
            $postUrl = $this->urlBuilder->getBaseUrl() . 'blog/' . $postUrlKey;
        } else {
            $postUrl = $this->urlBuilder->getUrl(
                'vconnect_blog/post/view',
                ['id' => $this->getEntityId(), '_secure' => true]
            );
        }

        return $postUrl;
    }
}
