<?php
declare(strict_types=1);

namespace VConnect\Blog\Service;

use VConnect\Blog\Model\ResourceModel\Post as PostResourceModel;
use VConnect\Blog\Api\Data\PostInterface;

class PostUrlKeyChecker
{
    /**
     * PostUrlKeyChecker constructor.
     * @param PostResourceModel $postResource
     */
    public function __construct(private PostResourceModel $postResource)
    {}

    /**
     * @param string $urlKey
     * @return string|null
     */
    public function checkUrlKey(string $urlKey): ?string
    {
        $connection = $this->postResource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(PostInterface::MAIN_TABLE), PostInterface::ENTITY_ID)
            ->where(PostInterface::URL_KEY . ' = ?', $urlKey);

        return $connection->fetchOne($select) ?: null;
    }
}
