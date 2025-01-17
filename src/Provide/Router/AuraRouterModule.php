<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Aura\Router\RouterContainer;
use BEAR\Sunday\Extension\Router\RouterInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class AuraRouterModule extends AbstractModule
{
    /** @param string $routerFile Router file path */
    public function __construct(private readonly string $routerFile = '', ?AbstractModule $module = null)
    {
        parent::__construct($module);
    }

    protected function configure(): void
    {
        $this->bind()->annotatedWith('aura_router_file')->toInstance($this->routerFile);
        $this->bind(RouterInterface::class)->toProvider(RouterCollectionProvider::class)->in(Scope::SINGLETON);
        $this->bind(RouterContainer::class)->toProvider(RouterContainerProvider::class)->in(Scope::SINGLETON);
        $this->bind(RouterInterface::class)->annotatedWith('primary_router')->to(AuraRouter::class);
    }
}
