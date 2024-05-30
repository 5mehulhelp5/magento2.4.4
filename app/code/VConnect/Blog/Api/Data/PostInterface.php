<?php
declare(strict_types=1);

namespace VConnect\Blog\Api\Data;

interface PostInterface
{
    public const MAIN_TABLE = 'blog_post_entity';
    public const ENTITY_ID = 'entity_id';
    public const TITLE = 'title';
    public const CONTENT = 'content';
    public const ANNOUNCE = 'announce';
    public const PUBLISH_DATE = 'publish_date';
    public const PUBLISH = 'publish';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self;

    /**
     * @return string
     */
    public function getAnnounce(): string;

    /**
     * @param string $announce
     * @return $this
     */
    public function setAnnounce(string $announce): self;

    /**
     * @return string
     */
    public function getPublishDate(): string;

    /**
     * @param string $publishDate
     * @return $this
     */
    public function setPublishDate(string $publishDate): self;

    /**
     * @return bool
     */
    public function getPublish(): bool;

    /**
     * @param bool $publish
     * @return $this
     */
    public function setPublish(bool $publish): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;
}
