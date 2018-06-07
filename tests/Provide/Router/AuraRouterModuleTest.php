<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\RouteCollection;
use Aura\Router\Router;
use BEAR\AppMeta\AppMeta;
use BEAR\Package\AppMetaModule;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Extension\Router\RouterInterface;
use FakeVendor\HelloWorld\Module\AppModule;
use Ray\Di\Injector;

class AuraRouterModuleTest extends \PHPUnit_Framework_TestCase
{
    public static $routerClass;

    public function testRouter()
    {
        $module = (new AuraRouterModule(null, new AppModule));
        $module->install(new AppMetaModule(new AppMeta('FakeVendor\HelloWorld')));
        $injector = new Injector($module);
        $router = $injector->getInstance(RouterInterface::class);
        $this->assertInstanceOf(RouterCollection::class, $router);
        $auraRouter = $injector->getInstance(RouterInterface::class, 'primary_router');
        $this->assertInstanceOf(AuraRouter::class, $auraRouter);
        $this->assertInstanceOf(RouteCollection::class, self::$routerClass);
    }

    public function testRouterFileNotExsits()
    {
        $this->expectException(InvalidRouterFilePathException::class);
        $module = (new AuraRouterModule('__INVALID', new AppModule));
        $module->install(new AppMetaModule(new AppMeta('FakeVendor\HelloWorld')));
        $injector = new Injector($module);
        $router = $injector->getInstance(RouterInterface::class);
    }

    public function testRouterFileExsits()
    {
        $module = (new AuraRouterModule(__DIR__ . '/aura.route.php', new AppModule));
        $module->install(new AppMetaModule(new AppMeta('FakeVendor\HelloWorld')));
        $injector = new Injector($module);
        $router = $injector->getInstance(RouterInterface::class);
        $this->assertInstanceOf(RouterCollection::class, $router);
    }
}
