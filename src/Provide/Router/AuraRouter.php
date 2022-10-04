<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use BEAR\Package\Provide\Router\Annotation\RequestHeaders; // phpcs:ignore
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use BEAR\Sunday\Extension\Router\NullMatch;
use BEAR\Sunday\Extension\Router\RouterInterface;
use BEAR\Sunday\Extension\Router\RouterMatch;
use Nyholm\Psr7\ServerRequest;
use Ray\Di\ProviderInterface;

use function file_get_contents;

/**
 * @psalm-import-type Globals from RouterInterface
 * @psalm-import-type Server from RouterInterface
 */
class AuraRouter implements RouterInterface
{
    /**
     *  Method over-ride parameter
     */
    public const METHOD_FILED = '_method';

    /**
     * Method over-ride header filed
     */
    public const METHOD_OVERRIDE_HEADER = 'HTTP_X_HTTP_METHOD_OVERRIDE';

    /** @var string */
    private $schemeHost = 'page://self';

    /** @var HttpMethodParamsInterface */
    private $httpMethodParams;

    /** @var Matcher */
    private $matcher;

    /** @var RouterContainer */
    private $routerContainer;

    /** @var ProviderInterface<array<string, string>>|null */
    private $headerProvider;

    /**
     * @param ProviderInterface<array<string, string>> $headerProvider
     *
     * @DefaultSchemeHost("schemeHost")
     * @RequestHeaders("headerProvider")
     */
    #[DefaultSchemeHost('schemeHost'), RequestHeaders('headerProvider')]
    public function __construct(
        RouterContainer $routerContainer,
        HttpMethodParamsInterface $httpMethodParams,
        string $schemeHost = 'page://self',
        ?ProviderInterface $headerProvider = null
    ) {
        $this->routerContainer = $routerContainer;
        $this->matcher = $routerContainer->getMatcher();
        $this->httpMethodParams = $httpMethodParams;
        $this->schemeHost = $schemeHost;
        $this->headerProvider = $headerProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-param array{REQUEST_METHOD: string, REQUEST_URI: string} $server
     */
    public function match(array $globals, array $server): RouterMatch
    {
        $psr15request = new ServerRequest(
            $server['REQUEST_METHOD'],
            $server['REQUEST_URI'],
            $this->headerProvider ? $this->headerProvider->get() : [],
            (string) file_get_contents('php://input'),
        );
        $route = $this->matcher->match($psr15request);
        if ($route === false) {
            return new NullMatch();
        }

        /** @psalm-suppress MixedArgumentTypeCoercion -- seems wrongly recognised? */
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
    private function getRouterMatch(array $globals, array $server, Route $route): RouterMatch
    {
        $request = new RouterMatch();

        // path
        $request->path = $this->schemeHost . $route->name;
        // method, query
        [$request->method, $query] = $this->httpMethodParams->get($server, $globals['_GET'], $globals['_POST']); // @phpstan-ignore-line
        /** @var array<string, mixed> $attributes */
        $attributes = $route->attributes;
        $request->query = $attributes + $query;

        return $request;
    }
}
