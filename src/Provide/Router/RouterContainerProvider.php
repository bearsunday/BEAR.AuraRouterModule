<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Aura\Router\RouterContainer;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

use function file_exists;

class RouterContainerProvider implements ProviderInterface
{
    /** @var string */
    private $schemeHost;

    /**
     * @var RouterContainer
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $routerContainer;

    /**
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(string $schemeHost)
    {
        $this->schemeHost = $schemeHost;
    }

    /**
     * @Inject
     * @Named("routerFile=aura_router_file")
     */
    public function setRouterContainer(AbstractAppMeta $appMeta, string $routerFile = ''): void
    {
        $this->routerContainer = new RouterContainer();
        $routerFile = $routerFile === '' ? $appMeta->appDir . '/var/conf/aura.route.php' : $routerFile;
        $map = $this->routerContainer->getMap();
        if (! file_exists($routerFile)) {
            throw new InvalidRouterFilePathException($routerFile);
        }

        require $routerFile;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->routerContainer;
    }
}
