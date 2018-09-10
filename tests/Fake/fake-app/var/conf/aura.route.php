<?php declare(strict_types=1);

/* @var $map \Aura\Router\Map */

$map->route('/user', '/user/{name}')->tokens(['name' => '[a-z]+']);
