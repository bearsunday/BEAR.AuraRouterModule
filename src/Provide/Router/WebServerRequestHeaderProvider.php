<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Ray\Di\ProviderInterface;

use function function_exists;
use function getallheaders;
use function is_scalar;
use function str_replace;
use function strtolower;
use function substr;
use function ucwords;

/** @implements ProviderInterface<array<string, string>> */
class WebServerRequestHeaderProvider implements ProviderInterface
{
    /** @return array<string, string> */
    public function get(): array
    {
        return function_exists('getallheaders') ? getallheaders() : $this->getAllHeaders();
    }

    /**
     * @return array<string, string>
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_' && is_scalar($value)) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = (string) $value;
            }
        }

        return $headers;
    }
}
