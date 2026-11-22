<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use BEAR\AppMeta\Meta;
use BEAR\Package\Module;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Extension\Router\NullMatch;
use BEAR\Sunday\Extension\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Ray\Di\AbstractModule;
use Ray\Di\Injector;

class AuraRouterModuleTest extends TestCase
{
    private const APP_NAME = 'FakeVendor\HelloWorld';
    private const CONTEXT = 'app';

    public function testGetInstance(): RouterInterface
    {
        $module = new AuraRouterModule('', $this->appModule());
        $injector = new Injector($module);
        $auraRouter = $injector->getInstance(RouterInterface::class, 'primary_router');
        $this->assertInstanceOf(AuraRouter::class, $auraRouter);

        return $auraRouter;
    }

    /** @depends testGetInstance */
    public function testRoute(AuraRouter $auraRouter): void
    {
        $globals = [
            '_GET' => [],
            '_POST' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/user/bear',
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertSame('get', $request->method);
        $this->assertSame('page://self/user', $request->path);
        $this->assertSame(['name' => 'bear'], $request->query);
    }

    /** @depends testGetInstance */
    public function testRouteWithTokenSuccess(AuraRouter $auraRouter): void
    {
        $globals = [
            '_GET' => [],
            '_POST' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/user/bear',
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertSame(['name' => 'bear'], $request->query);
    }

    /** @depends testGetInstance */
    public function testRouteWithTokenFailure(AuraRouter $auraRouter): void
    {
        $globals = [
            '_GET' => [],
            '_POST' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/user/0bear',
        ];
        $request = $auraRouter->match($globals, $server);
        $this->assertInstanceOf(NullMatch::class, $request);
    }

    public function testRouterFileNotExsits(): void
    {
        $this->expectException(InvalidRouterFilePathException::class);
        $module = new AuraRouterModule('__INVALID', $this->appModule());
        $injector = new Injector($module);
        $injector->getInstance(RouterInterface::class);
    }

    public function testRouterFileExsits(): void
    {
        $module = new AuraRouterModule(__DIR__ . '/aura.route.php', $this->appModule());
        $injector = new Injector($module);
        $router = $injector->getInstance(RouterInterface::class);
        $this->assertInstanceOf(RouterCollection::class, $router);
    }

    private function appModule(): AbstractModule
    {
        return (new Module())(new Meta(self::APP_NAME), self::CONTEXT);
    }
}
