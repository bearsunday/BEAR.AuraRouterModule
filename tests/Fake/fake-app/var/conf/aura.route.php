<?php

/* @var $map \Aura\Router\Map */

$map->route('/blog', '/blog/{id}');
$map->route('/user', '/user/{name}')->tokens(['name' => '[a-z]+']);
