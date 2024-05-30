<?php

namespace VConnect\Blog\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use VConnect\Blog\Api\Data\PostInterfaceFactory;
use VConnect\Blog\Model\ResourceModel\Post as PostResource;
use \Magento\Framework\Exception\AlreadyExistsException;

class AddTestPostRecordToTable implements DataPatchInterface
{
    private PostResource $postResource;
    private PostInterfaceFactory $postFactory;

    public function __construct(PostResource $postResource, PostInterfaceFactory $postFactory)
    {
        $this->postResource = $postResource;
        $this->postFactory = $postFactory;
    }

    /**
     * @return DataPatchInterface
     * @throws AlreadyExistsException
     */
    public function apply(): DataPatchInterface
    {
        $post = $this->postFactory->create();
        $post->setTitle('test post two')
            ->setContent('test post two content')
            ->setAnnounce('test post two announce data');
        $this->postResource->save($post);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
