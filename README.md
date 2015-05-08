# BEAR.AuraRouterModule

This is the [Aura Router (v2)](https://github.com/auraphp/Aura.Router/tree/2.x) Module for BEAR.Sunday.


## Installation

### Composer install

```
composer require bear/aura-router-module
```

### Module install

```php
use Ray\Di\AbstractModule;
use BEAR\Package\Provide\Router\AuraRouterModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new AuraRouterModule);
    }
}
```

### Router config

place router script file at `var/conf/aura.route.php`.

```php
<?php
/** @var $router \Aura\Router\RouteCollection */

$router->add('/weekday', '/weekday/{year}/{month}/{day}')->addValues(['path' => '/weekday']);
```
