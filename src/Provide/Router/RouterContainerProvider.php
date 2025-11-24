<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Aura\Router\RouterContainer;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

use function file_exists;

/** @implements ProviderInterface<RouterContainer> */
class RouterContainerProvider implements ProviderInterface
{
    private readonly RouterContainer $routerContainer;

    public function __construct(AbstractAppMeta $appMeta, #[Named('aura_router_file')] string $routerFile = '')
    {
        $this->routerContainer = new RouterContainer();
        $routerFile = $routerFile === '' ? $appMeta->appDir . '/var/conf/aura.route.php' : $routerFile;
        //  $map is required in $routerFile
        $map = $this->routerContainer->getMap();
        if (! file_exists($routerFile)) {
            throw new InvalidRouterFilePathException($routerFile);
        }

        require $routerFile;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): RouterContainer
    {
        return $this->routerContainer;
    }
}
