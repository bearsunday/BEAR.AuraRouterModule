<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use BEAR\Sunday\Extension\Router\RouterInterface;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Zend\Diactoros\ServerRequestFactory;

class AuraRouter implements RouterInterface
{
    const METHOD_FILED = '_method';

    const METHOD_OVERRIDE_HEADER = 'HTTP_X_HTTP_METHOD_OVERRIDE';

    /**
     * @var string
     */
    private $schemeHost = 'page://self';

    /**
     * @var HttpMethodParamsInterface
     */
    private $httpMethodParams;

    private $matcher;

    private $routerContainer;

    /**
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(RouterContainer $routerContainer, $schemeHost, HttpMethodParamsInterface $httpMethodParams)
    {
        $this->routerContainer = $routerContainer;
        $this->matcher = $routerContainer->getMatcher();
        $this->schemeHost = $schemeHost;
        $this->httpMethodParams = $httpMethodParams;
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $globals, array $server)
    {
        $psr15request = ServerRequestFactory::fromGlobals(
            $server,
            $globals['_GET'],
            $globals['_POST'],
            [],
            []
        );
        $route = $this->matcher->match($psr15request);
        if ($route === false) {
            return false;
        }

        return $this->getRequest($globals, $server, $route);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $data)
    {
        try {
            return $this->routerContainer->getGenerator()->generate($name, $data);
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

        // path
        $path = $route->name;
        $path = substr($path, 0, 1) === '/' ? $this->schemeHost . $path : $path;
        $request->path = $path;
        // query
        list($request->method, $query) = $this->httpMethodParams->get($server, $globals['_GET'], $globals['_POST']);
        $request->query = $route->attributes + $query;

        return $request;
    }
}
