<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Route;
use Aura\Router\Router;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use BEAR\Sunday\Extension\Router\RouterInterface;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Ray\Di\Provider;

class AuraRouter implements RouterInterface
{
    const METHOD_FILED = '_method';

    const METHOD_OVERRIDE_HEADER = 'HTTP_X_HTTP_METHOD_OVERRIDE';

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Provider
     */
    private $routerProvider;

    /**
     * @var string
     */
    private $schemeHost = 'page://self';

    /**
     * @var HttpMethodParamsInterface
     */
    private $httpMethodParams;

    /**
     * @var string
     */
    private $routerFile;

    /**
     * @param Router                    $router
     * @param string                    $schemeHost
     * @param HttpMethodParamsInterface $httpMethodParams
     * @param string                    $routerFile
     *
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(Router $router, $schemeHost, HttpMethodParamsInterface $httpMethodParams, $routerFile)
    {
        $this->schemeHost = $schemeHost;
        $this->router = $router;
        $this->httpMethodParams = $httpMethodParams;
        $this->routerFile = $routerFile;
        $this->__wakeup();
    }

    public function __wakeup()
    {
        /** @global $router */
        $router = $this->router->getRoutes();
        include $this->routerFile;
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $globals, array $server)
    {
        $path = parse_url($server['REQUEST_URI'], PHP_URL_PATH);
        $route = $this->router->match($path, $server);
        if ($route === false) {
            return false;
        }
        $request = $this->getRequest($globals, $server, $route);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $data)
    {
        try {
            return $this->router->generate($name, $data);
        } catch (RouteNotFound $e) {
            return false;
        }
    }

    /**
     * Return resource request
     *
     * @param array $globals
     * @param array $server
     * @param Route $route
     *
     * @return RouterMatch
     */
    private function getRequest(array $globals, array $server, Route $route)
    {
        $request = new RouterMatch;

        $params = $route->params;
        unset($params['action']);
        // path
        $path = substr($params['path'], 0, 1) === '/' ? $this->schemeHost . $params['path'] : $params['path'];
        $request->path = $path;
        // query
        unset($params['path']);
        list($method, $query) = $this->httpMethodParams->get($server, $globals['_GET'], $globals['_POST']);
        $params += $query;
        unset($params[self::METHOD_FILED]);
        $request->query = $params;
        // method
        $request->method = $method;

        return $request;
    }
}
