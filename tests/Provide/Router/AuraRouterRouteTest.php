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

class AuraRouterRouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuraRoute
     */
    private $route;

    public function setUp()
    {
        $this->route = new AuraRoute(new RouteCollection(new RouteFactory), new Generator);
    }

    public function testRoute()
    {
        $this->route->route('/user', '/user/{id}');
        $routes = $this->route->getRoutes();
        $expect = [
            'tokens' => [],
            'server' => [],
            'method' => [],
            'values' => ['action' => '/user', 'path' => '/user'],
            'secure' => null,
            'wildcard' => null,
            'routable' => true,
            'is_match' => null,
            'generate' => null,
        ];
        $this->assertRoute($expect, $routes['/user']);
    }

    protected function assertRoute($expect, $actual)
    {
        foreach ($expect as $key => $val) {
            $this->assertSame($val, $actual->$key);
        }
    }
}
