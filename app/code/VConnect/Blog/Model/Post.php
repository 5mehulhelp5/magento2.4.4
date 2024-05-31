<?php

namespace VConnect\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use VConnect\Blog\Api\Data\PostInterface;

class Post extends AbstractModel implements PostInterface
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\VConnect\Blog\Model\ResourceModel\Post::class);
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
     * @return string
     */
    public function getContent(): string
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
     * @return string
     */
    public function getAnnounce(): string
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
     * @return string
     */
    public function getPublishDate(): string
    {
        return $this->getData(self::PUBLISH_DATE);
    }

    /**
     * @param string $publishDate
     * @return PostInterface
     */
    public function setPublishDate(string $publishDate): PostInterface
    {
        return $this->setData(self::PUBLISH_DATE, $publishDate);
    }

    /**
     * @return bool
     */
    public function getPublish(): bool
    {
        return $this->getData(self::PUBLISH);
    }

    /**
     * @param bool $publish
     * @return PostInterface
     */
    public function setPublish(bool $publish): PostInterface
    {
        return $this->setData(self::PUBLISH, $publish);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return PostInterface
     */
    public function setCreatedAt(string $createdAt): PostInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return PostInterface
     */
    public function setUpdatedAt(string $updatedAt): PostInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
