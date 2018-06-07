<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Generator;
use Aura\Router\RouteCollection;
use Aura\Router\RouteFactory;

class AuraRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuraRoute
     */
    private $routerAdapter;

    /**
     * @var AuraRouter
     */
    private $auraRouter;

    public function setUp()
    {
        parent::setUp();
        $this->routerAdapter = new AuraRoute(
            new RouteCollection(new RouteFactory),
            new Generator,
            null
        );
        $routerFile = dirname(__DIR__, 2) . '/Fake/fake-app/var/conf/aura.route.php';
        $this->auraRouter = new AuraRouter($this->routerAdapter, 'page://self', new HttpMethodParams, $routerFile);
    }

    public function testMatch()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}');
        $globals = [
            '_GET' => [],
            '_POST' => ['title' => 'hello']
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment'
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('post', $request->method);
        $this->assertSame('page://self/blog', $request->path);
        $this->assertSame(['id' => 'PC6001', 'title' => 'hello'], $request->query);
    }

    public function testMatchInvalidToken()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}')->addTokens(['id' => '\d+']);
        $globals = [
            '_GET' => [],
            '_POST' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001'
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertFalse($request);
    }

    public function testMatchValidToken()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}')->addTokens(['id' => '\d+']);
        $globals = [
            '_GET' => [],
            '_POST' => ['title' => 'hello']
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/1'
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('page://self/blog', $request->path);
        $this->assertSame(['id' => '1', 'title' => 'hello'], $request->query);
    }

    public function testMethodOverrideField()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [AuraRouter::METHOD_FILED => 'PUT', 'title' => 'hello'],
            '_GET' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment'
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('put', $request->method);
        $this->assertSame(['id' => 'PC6001', 'title' => 'hello'], $request->query);
    }

    public function testMethodOverrideHeader()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [AuraRouter::METHOD_FILED => 'PUT'],
            '_GET' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://localhost/blog/PC6001?query=value#fragment',
            'HTTP_X_HTTP_METHOD_OVERRIDE' => 'DELETE'
        ];
        $request = $this->auraRouter->match($globals, $server);
        $this->assertSame('put', $request->method);
        $this->assertSame(['id' => 'PC6001'], $request->query);
    }

    public function testNotMatch()
    {
        $this->routerAdapter->route('/blog', '/blog/{id}');
        $globals = [
            '_POST' => [],
            '_GET' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/not_much_uri',
        ];
        $match = $this->auraRouter->match($globals, $server);
        $this->assertFalse($match);
    }

    public function testInvalidPath()
    {
        $globals = [
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => null
        ];
        $match = $this->auraRouter->match($globals, $server);
        $this->assertFalse($match);
    }

    public function routerProvider()
    {
        $this->setUp();

        return [
            [$this->auraRouter],
            [unserialize(serialize($this->auraRouter))]
        ];
    }

    /**
     * @dataProvider routerProvider
     */
    public function testSeriaizetestMatch($router)
    {
        /** @var AuraRouter $router */
        $globals = [
            '_POST' => [],
            '_GET' => []
        ];
        $server = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'http://localhost/user/10'
        ];
        $request = $router->match($globals, $server);
        $this->assertSame('get', $request->method);
        $this->assertSame('page://self/user', $request->path);
        $this->assertSame(['id' => '10'], $request->query);
    }
}
