<?php declare(strict_types=1);
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

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

    /**
     * @param string              $routerFile Router file path
     * @param AbstractModule|null $module
     */
    public function __construct(string $routerFile = '', AbstractModule $module = null)
    {
        $this->routerFile = $routerFile;
        parent::__construct($module);
    }

    protected function configure() : void
    {
        $this->bind()->annotatedWith('aura_router_file')->toInstance($this->routerFile);
        $this->bind(RouterInterface::class)->toProvider(RouterCollectionProvider::class)->in(Scope::SINGLETON);
        $this->bind(RouterContainer::class)->toProvider(RouterContainerProvider::class)->in(Scope::SINGLETON);
        $this->bind(RouterInterface::class)->annotatedWith('primary_router')->to(AuraRouter::class);
    }
}
