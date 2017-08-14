<?php
/**
 * This file is part of the BEAR.AuraRouterModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;


use Aura\Router\Router;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

class AuraRouterProvider implements ProviderInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $schemeHost;

    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    /**
     * @param AuraRoute $router
     *
     * @Inject
     * @Named("aura_router")
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(AbstractAppMeta $appMeta, $schemeHost)
    {
        $this->schemeHost = $schemeHost;
        $this->appMeta = $appMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $routeFile = $this->appMeta->appDir . '/var/conf/aura.route.php';
        $router = $this->router; // global
        include $routeFile;

        return new AuraRouter($this->router, $this->schemeHost, new HttpMethodParams);
    }
}
