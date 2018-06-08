<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Map;
use Aura\Router\Router;
use Aura\Router\RouterContainer;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

class AuraRouterProvider implements ProviderInterface
{
    /**
     * @var string
     */
    private $schemeHost;

    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    /**
     * @var string
     */
    private $routerFile;

    /**
     * @var RouterContainer
     */
    private $routerContainer;

    /**
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(AbstractAppMeta $appMeta, string $schemeHost, RouterContainer $routerContainer)
    {
        $this->schemeHost = $schemeHost;
        $this->appMeta = $appMeta;
        $this->routerContainer = $routerContainer;
    }

    /**
     * @Inject
     * @Named("router=aura_map,routerFile=aura_router_file")
     */
    public function setRouter(string $routerFile = null)
    {
        $this->routerFile = ($routerFile === null) ? $this->appMeta->appDir . '/var/conf/aura.route.php' : $routerFile;
        if (! \file_exists($this->routerFile)) {
            throw new InvalidRouterFilePathException($this->routerFile);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $map = $this->routerContainer->getMap(); // global

        include $this->routerFile;

        return new AuraRouter($this->routerContainer, $this->schemeHost, new HttpMethodParams, $this->routerFile);
    }
}
