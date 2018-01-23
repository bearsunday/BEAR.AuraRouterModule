<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use BEAR\Sunday\Extension\Router\RouterInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class AuraRouterModule extends AbstractModule
{
    /**
     * @var string
     */
    private $routerFile;

    public function __construct($routerFile = null, AbstractModule $module = null)
    {
        $this->routerFile = $routerFile;
        parent::__construct($module);
    }

    protected function configure()
    {
        $this->bind(RouterContainer::class);
        $this->bind()->annotatedWith('aura_router_file')->toInstance($this->routerFile);
        $this->bind(Map::class)->annotatedWith('aura_map')->to(Map::class);
        $this->bind(Map::class)->in(Scope::SINGLETON);
        $this->bind(RouterInterface::class)->annotatedWith('primary_router')->toProvider(AuraRouterProvider::class);
        $this->bind(RouterInterface::class)->toProvider(RouterCollectionProvider::class)->in(Scope::SINGLETON);
    }
}
