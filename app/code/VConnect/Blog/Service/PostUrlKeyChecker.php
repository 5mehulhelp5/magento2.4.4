<?php
declare(strict_types=1);

namespace VConnect\Blog\Service;

use VConnect\Blog\Model\ResourceModel\Post as PostResourceModel;

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
     * @return string
     */
    public function checkUrlKey(string $urlKey): string
    {
        $connection = $this->postResource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName('blog_post_entity'), 'entity_id')
            ->where('url_key = ?', $urlKey);

        $result = $connection->fetchOne($select);
        if (!$result) {
            $result = '';
        }

        return $result;
    }
}
