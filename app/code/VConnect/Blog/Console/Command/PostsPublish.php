<?php
declare(strict_types=1);

namespace VConnect\Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VConnect\Blog\Model\PostsPublishManager;

class PostsPublish extends Command
{
    /**
     * PostsPublish constructor.
     * @param PostsPublishManager $publishManager
     * @param string|null $name
     */
    public function __construct(
        private PostsPublishManager $publishManager,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Console command to publish blog posts, ' .
            'which `publish_date` is lower than current date with time, and `publish` status is false.'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->publishManager->execute();
            if ($this->publishManager->getPublishOperationResult()) {
                $output->writeln("Post(s) were successfully published.");
            } else {
                $output->writeln("There are no posts to publish.");
            }
        } catch (\Exception) {
            parent::execute($input, $output);
        }
    }
}
