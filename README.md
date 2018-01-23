# BEAR.AuraRouterModule

[![Build Status](https://travis-ci.org/bearsunday/BEAR.AuraRouterModule.svg?branch=2.x)](https://travis-ci.org/bearsunday/BEAR.AuraRouterModule)

[Aura Router v3](https://github.com/auraphp/Aura.Router/tree/3.x) Module for BEAR.Sunday.


## Installation

### Composer install

```
composer require bear/aura-router-module 2.x-dev
```

### Module install

```php
use Ray\Di\AbstractModule;
use BEAR\Package\Provide\Router\AuraRouterModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new AuraRouterModule('/path/to/aura.route.php');
    }
}
```

### Defining Routes

Place router script file at `var/conf/aura.route.php`.

```php
<?php
/* @var $map \Aura\Router\Map */

$map->route('/weekday', '/weekday/{year}/{month}/{day}');
$map->get('archive', '/archive{/year,month,day}')
    ->tokens([
        'year' => '\d{4}',
        'month' => '\d{2}',
        'day' => '\d{2}',
    ]); // Optional Placeholder Tokens
```

See more rules at [Aura.Router v3 documentation](https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md).
