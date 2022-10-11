<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use BEAR\Package\Provide\Router\Annotation\RequestHeaders;
use Ray\Di\AbstractModule;
use Ray\Di\ProviderInterface;

class RequestHeaderModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->bind(ProviderInterface::class)->annotatedWith(RequestHeaders::class)->to(WebServerRequestHeaderProvider::class);
    }
}
