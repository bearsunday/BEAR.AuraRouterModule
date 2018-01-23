<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Map;
use Aura\Router\RouterContainer;

class AuraRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Map
     */
    private $matcher;

    /**
     * @var AuraRouter
     */
    private $auraRouter;

    public function setUp()
    {
        parent::setUp();
        $routerContainer = new RouterContainer;
        $this->matcher = $routerContainer->getMap();
        $this->auraRouter = new AuraRouter($routerContainer, 'page://self', new HttpMethodParams);
    }

    public function testMatch()
    {
        $this->matcher->route('/blog', '/blog/{id}');
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
        $this->matcher->route('/blog', '/blog/{id}')->tokens(['id' => '\d+']);
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
        $this->matcher->route('/blog', '/blog/{id}')->tokens(['id' => '\d+']);
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
        $this->matcher->route('/blog', '/blog/{id}');
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
        $this->matcher->route('/blog', '/blog/{id}');
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
        $this->matcher->route('/blog', '/blog/{id}');
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
}
