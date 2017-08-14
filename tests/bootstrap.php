<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
/* @var $loader \Composer\Autoload\ClassLoader */
$loader->addPsr4('BEAR\Package\\', __DIR__ . '/Fake');
$loader->addPsr4('FakeVendor\HelloWorld\\', __DIR__ . '/Fake/fake-app/src');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);
