<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Router as AuraRouterRouter;

/**
 * An extended router for BEAR.Sunday
 */
class AuraRoute extends AuraRouterRouter
{
    /**
     * Adds a route
     *
     * @param string $name
     * @param string $path
     *
     * @return $this
     */
    public function route($name, $path)
    {
        return $this->add($name, $path)->addValues(['path' => $name]);
    }
}
