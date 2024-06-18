<?php
declare(strict_types=1);

namespace VConnect\Blog\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use VConnect\Blog\Service\PostUrlKeyChecker;

class Router implements RouterInterface
{
    /**
     * Router constructor.
     * @param \VConnect\Blog\Service\PostUrlKeyChecker $postUrlKeyChecker
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(
        private PostUrlKeyChecker $postUrlKeyChecker,
        private ActionFactory $actionFactory
    ) {}

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        $pathInfo = trim((string)$request->getPathInfo(), '/');

        $pathInfoParts = explode('/', $pathInfo);
        if (!empty($pathInfoParts[0]) && $pathInfoParts[0] === 'blog' && !empty($pathInfoParts[1])) {
            $urlKey = $pathInfoParts[1];
        } else {
            return null;
        }

        $postId = $this->postUrlKeyChecker->checkUrlKey($urlKey);
        if (empty($postId)) {
            return null;
        }

        $request
            ->setModuleName('vconnect_blog')
            ->setControllerName('post')
            ->setActionName('view')
            ->setParam('id', $postId);
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
        $request->setPathInfo($urlKey);

        return $this->actionFactory->create(Forward::class);
    }
}
