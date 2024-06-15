<?php
declare(strict_types=1);

namespace VConnect\Blog\Cron;

use Psr\Log\LoggerInterface;
use VConnect\Blog\Model\PostsPublishManager;

class PostsPublisher
{
    public function __construct(
        private PostsPublishManager $publishManager,
        private LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        try {
            $this->publishManager->execute();
        } catch (\Exception $exception) {
            $this->logger->notice($exception->getMessage());
        }
    }
}
