<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use BEAR\Sunday\Extension\Router\NullMatch;
use PHPUnit\Framework\TestCase;

use function dirname;
use function serialize;
use function unserialize;

class AuraRouterTest extends TestCase
{
    /** @var Map<array<string>> */
    private $map;

    /** @var AuraRouter */
    private $auraRouter;

    protected function setUp(): void
    {
        parent::setUp();
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();
        $this->map = $map;
        $routerFile = dirname(__DIR__, 2) . '/Fake/fake-app/var/conf/aura.route.php';
        require $routerFile;
        $this->auraRouter = new AuraRouter($routerContainer, new HttpMethodParams());
    }

    public function testMatch(): void
    {
        $this->map->route('/blog', '/blog/{id}');
        $globals = [
            '_GET' => [],
            '_POST' => ['title' => 'hello'],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment',
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('post', $request->method);
        $this->assertSame('page://self/blog', $request->path);
        $this->assertSame(['id' => 'PC6001', 'title' => 'hello'], $request->query);
    }

    public function testMatchInvalidToken(): void
    {
        $this->map->route('/blog', '/blog/{id}')->tokens(['id' => '\d+']);
        $globals = [
            '_GET' => [],
            '_POST' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001',
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertInstanceOf(NullMatch::class, $request);
    }

    public function testMatchValidToken(): void
    {
        $this->map->route('/blog', '/blog/{id}')->tokens(['id' => '\d+']);
        $globals = [
            '_GET' => [],
            '_POST' => ['title' => 'hello'],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/1',
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('page://self/blog', $request->path);
        $this->assertSame(['id' => '1', 'title' => 'hello'], $request->query);
    }

    public function testMethodOverrideField(): void
    {
        $this->map->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [AuraRouter::METHOD_FILED => 'PUT', 'title' => 'hello'],
            '_GET' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment',
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('put', $request->method);
        $this->assertSame(['id' => 'PC6001', 'title' => 'hello'], $request->query);
    }

    public function testMethodOverrideHeader(): void
    {
        $this->map->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [AuraRouter::METHOD_FILED => 'PUT'],
            '_GET' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment',
            'HTTP_X_HTTP_METHOD_OVERRIDE' => 'DELETE',
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('put', $request->method);
        $this->assertSame(['id' => 'PC6001'], $request->query);
    }

    public function testNotMatch(): void
    {
        $this->map->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [],
            '_GET' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/not_much_uri',
        ];
        $match = $this->auraRouter->match($globals, $server);
        $this->assertInstanceOf(NullMatch::class, $match);
    }

    public function testInvalidPath(): void
    {
        $globals = [
            '_POST' => [],
            '_GET' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '',
        ];
        $match = $this->auraRouter->match($globals, $server);
        $this->assertInstanceOf(NullMatch::class, $match);
    }

    public function testGenerate(): void
    {
        $this->map->route('/calendar', '/calendar/{year}/{month}');
        $uri = $this->auraRouter->generate('/calendar', ['year' => '8', 'month' => '1']);
        $this->assertSame('/calendar/8/1', $uri);
    }

    public function testGenerateFailed(): void
    {
        $uri = $this->auraRouter->generate('/_invalid_', ['year' => '8', 'month' => '1']);
        $this->assertFalse((bool) $uri);
    }

    public function testSerialize(): void
    {
        /** @var AuraRouter $router */
        $router = unserialize(serialize($this->auraRouter));
        $globals = [
            '_GET' => [],
            '_POST' => [],
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/user/bear',
        ];
        $request = $router->match($globals, $server);
        $this->assertSame('get', $request->method);
        $this->assertSame('page://self/user', $request->path);
        $this->assertSame(['name' => 'bear'], $request->query);
    }
}
