<?php

/* @var $router \Aura\Router\Map */

$router->route('/blog', '/blog/{id}');
$router->route('/user', '/user/{name}')->tokens(['name' => '[a-z]+']);
