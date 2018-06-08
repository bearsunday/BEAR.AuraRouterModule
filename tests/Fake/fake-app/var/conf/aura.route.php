<?php

/* @var $map \Aura\Router\Map */

$map->route('/user', '/user/{name}')->tokens(['name' => '[a-z]+']);
