<?php declare(strict_types=1);
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use BEAR\AppMeta\AppMeta;
use BEAR\Package\AppMetaModule;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Extension\Router\NullMatch;
use BEAR\Sunday\Extension\Router\RouterInterface;
use FakeVendor\HelloWorld\Module\AppModule;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector;

class AuraRouterModuleTest extends TestCase
{
    public static $routerClass;

    public function testGetInstance()
    {
        $module = (new AuraRouterModule('', new AppModule));
        $module->install(new AppMetaModule(new AppMeta('FakeVendor\HelloWorld')));
        $injector = new Injector($module);
        $auraRouter = $injector->getInstance(RouterInterface::class, 'primary_router');
        $this->assertInstanceOf(AuraRouter::class, $auraRouter);

        return $auraRouter;
    }

    /**
     * @depends testGetInstance
     */
    public function testRoute(AuraRouter $auraRouter)
    {
        $globals = [
            '_GET' => [],
            '_POST' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/user/bear'
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertSame('get', $request->method);
        $this->assertSame('page://self/user', $request->path);
        $this->assertSame(['name' => 'bear'], $request->query);
    }

    /**
     * @depends testGetInstance
     */
    public function testRouteWithTokenSuccess(AuraRouter $auraRouter)
    {
        $globals = [
            '_GET' => [],
            '_POST' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/user/bear'
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertSame(['name' => 'bear'], $request->query);
    }

    /**
     * @depends testGetInstance
     */
    public function testRouteWithTokenFailure(AuraRouter $auraRouter)
    {
        $globals = [
            '_GET' => [],
            '_POST' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/user/0bear'
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertInstanceOf(NullMatch::class, $request);
    }

    public function testRouterFileNotExsits()
    {
        $this->expectException(InvalidRouterFilePathException::class);
        $module = (new AuraRouterModule('__INVALID', new AppModule));
        $module->install(new AppMetaModule(new AppMeta('FakeVendor\HelloWorld')));
        $injector = new Injector($module);
        $injector->getInstance(RouterInterface::class);
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
