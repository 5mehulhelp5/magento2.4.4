<?php
declare(strict_types=1);

namespace VConnect\Blog\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteria;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use VConnect\Blog\Api\Data\PostInterface;
use VConnect\Blog\Api\Data\PostSearchResultsInterface;
use VConnect\Blog\Api\PostRepositoryInterface;
use VConnect\Blog\Model\PostsPublishManager;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * @covers \VConnect\Blog\Model\PostsPublishManager
 */
class PostsPublishManagerTest extends TestCase
{
    private MockObject $postRepository;
    private MockObject $searchCriteriaBuilder;
    private MockObject $searchCriteria;
    private MockObject $searchResult;

    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchResult = $this->createMock(PostSearchResultsInterface::class);

        $this->searchCriteriaBuilder->expects($this->any())->method('addFilter')->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->once())->method('create')->willReturn($this->searchCriteria);
    }


    public function testExecuteWithNoPostsToPublish()
    {
        $this->searchResult->expects($this->once())->method('getItems')->willReturn([]);
        $this->postRepository->expects($this->once())->method('getList')->willReturn($this->searchResult);

        $publishManager = new PostsPublishManager(
            $this->postRepository,
            $this->searchCriteriaBuilder
        );

        $publishManager->execute();
        $this->assertFalse($publishManager->getPublishOperationResult());
    }

    public function testExecuteWithPostsToPublish()
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects($this->any())->method('setPublish')->with(true);
        $post->expects($this->any())->method('getPublish')->willReturn(true);
        $this->searchResult->expects($this->once())->method('getItems')->willReturn([$post]);
        $this->postRepository->expects($this->once())->method('getList')->willReturn($this->searchResult);
        $this->postRepository->expects($this->any())->method('save')->with($post);

        $publishManager = new PostsPublishManager(
            $this->postRepository,
            $this->searchCriteriaBuilder
        );

        $beforeResult = $post->getPublish();
        $publishManager->execute();
        $this->assertSame($beforeResult, $post->getPublish()); // probably redundant , just for testng purposes
        $this->assertTrue($publishManager->getPublishOperationResult());
    }
}
