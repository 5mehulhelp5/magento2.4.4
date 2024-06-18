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
    public const URL_KEY = 'url_key';

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

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
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self;

    /**
     * @return string|null
     */
    public function getAnnounce(): ?string;

    /**
     * @param string $announce
     * @return $this
     */
    public function setAnnounce(string $announce): self;

    /**
     * @return string|null
     */
    public function getPublishDate(): ?string;

    /**
     * @param string|null $publishDate
     * @return $this
     */
    public function setPublishDate(?string $publishDate = null): self;

    /**
     * @return bool
     */
    public function getPublish(): bool;

    /**
     * @param bool $publish
     * @return $this
     */
    public function setPublish(?bool $publish = null): self;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt(?string $createdAt = null): self;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?string $updatedAt = null): self;

    /**
     * @return string|null
     */
    public function getUrlKey(): ?string;

    /**
     * @param string|null $urlKey
     * @return $this
     */
    public function setUrlKey(?string $urlKey = null): self;

    /**
     * Returns url path of post page
     *
     * @return string
     */
    public function getUrl(): string;
}
