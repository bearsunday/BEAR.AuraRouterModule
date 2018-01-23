<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Map;
use Aura\Router\Route;

/**
 * An extended router for BEAR.Sunday
 */
class AuraRoute extends Map
{
    public function __construct(Route $protoRoute)
    {
        parent::__construct($protoRoute);
    }

    /**
     * Adds a route
     *
     * @param string $name
     * @param string $path
     */
    public function route($name, $path, $handler = null)
    {
        parent::route($name, $path);
    }
}
