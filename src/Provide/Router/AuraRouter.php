<?php declare(strict_types=1);
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
use BEAR\Sunday\Extension\Router\NullMatch;
use BEAR\Sunday\Extension\Router\RouterInterface;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Laminas\Diactoros\ServerRequest;
use Ray\Di\Di\Inject;

/**
 * @psalm-import-type Globals from RouterInterface
 * @psalm-import-type Server from RouterInterface
 */
class AuraRouter implements RouterInterface
{
    /**
     *  Method over-ride parameter
     */
    const METHOD_FILED = '_method';

    /**
     * Method over-ride header filed
     */
    const METHOD_OVERRIDE_HEADER = 'HTTP_X_HTTP_METHOD_OVERRIDE';

    /**
     * @var string
     */
    private $schemeHost = 'page://self';

    /**
     * @var HttpMethodParamsInterface
     */
    private $httpMethodParams;

    /**
     * @var \Aura\Router\Matcher
     */
    private $matcher;

    /**
     * @var RouterContainer
     */
    private $routerContainer;

    public function __construct(RouterContainer $routerContainer, HttpMethodParamsInterface $httpMethodParams)
    {
        $this->routerContainer = $routerContainer;
        $this->matcher = $routerContainer->getMatcher();
        $this->httpMethodParams = $httpMethodParams;
    }

    /**
     * @DefaultSchemeHost("schemeHost")
     * @Inject
     */
    public function setSchemaHost(string $schemeHost) : void
    {
        $this->schemeHost = $schemeHost;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-param array{REQUEST_METHOD: string, REQUEST_URI: string} $server
     */
    public function match(array $globals, array $server) : RouterMatch
    {
        $psr15request = new ServerRequest(
            $server,
            [],
            $server['REQUEST_URI'],
            $server['REQUEST_METHOD'],
            'php://input',
            [],
            [],
            $globals['_GET'],
            $globals['_POST']
        );
        $route = $this->matcher->match($psr15request);
        if ($route === false) {
            return new NullMatch;
        }

        return $this->getRouterMatch($globals, $server, $route);
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
     * @psalm-param Globals $globals
     * @psalm-param Server $server
     * @phpstan-param array<string, mixed> $globals
     * @phpstan-param array{REQUEST_METHOD: string} $server
     */
    private function getRouterMatch(array $globals, array $server, Route $route) : RouterMatch
    {
        $request = new RouterMatch;

        // path
        $request->path = $this->schemeHost . $route->name;
        // method, query
        [$request->method, $query] = $this->httpMethodParams->get($server, $globals['_GET'], $globals['_POST']);
        $request->query = $route->attributes + $query;

        return $request;
    }
}
