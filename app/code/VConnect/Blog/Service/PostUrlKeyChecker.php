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
     * @return string|null
     */
    public function checkUrlKey(string $urlKey): ?string
    {
        $connection = $this->postResource->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName('blog_post_entity'), 'entity_id')
            ->where('url_key = ?', $urlKey);

        $result = $connection->fetchOne($select);
        if (!$result) {
            $result = $this->checkPostId($urlKey);
        }

        return $result;
    }

    private function checkPostId(string $urlKey): ?string
    {
        $result = null;

        $postUrlParts = explode('-', $urlKey);
        if (!empty($postUrlParts[0]) && $postUrlParts[0] === 'post' && !empty($postUrlParts[1])) {
            $postId = $postUrlParts[1];
            if (is_numeric($postId) && !is_float($postId) && (int)$postId > 0) {
                $connection = $this->postResource->getConnection();
                $select = $connection->select()
                    ->from($connection->getTableName('blog_post_entity'), 'entity_id')
                    ->where('entity_id = ?', $postId);

                $result = $connection->fetchOne($select) ?: null;
            }
        }

        return $result;
    }
}
