<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router;

use Ray\Di\ProviderInterface;

use function function_exists;
use function getallheaders;
use function is_scalar;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function substr;
use function ucwords;

/** @implements ProviderInterface<array<string, string>> */
class WebServerRequestHeaderProvider implements ProviderInterface
{
    /** @return array<string, string> */
    public function get(): array
    {
        // @codeCoverageIgnoreStart
        if (function_exists('getallheaders')) {
            /** @var array<string, string> $headers */
            $headers = getallheaders();

            return $headers;
        }

        // @codeCoverageIgnoreEnd

        return $this->getAllHeaders();
    }

    /**
     * @return array<string, string>
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    private function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with((string) $name, 'HTTP_') && is_scalar($value)) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr((string) $name, 5)))))] = (string) $value;
            }
        }

        return $headers;
    }
}
