<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ray\Di\Di\Qualifier;

/**
 * @Annotation
 * @Target("METHOD")
 * @Qualifier
 * @NamedArgumentConstructor
 * @deprecated 3.0.0 This attribute is no longer used. WebServerRequestHeaderProvider is now directly injected into AuraRouter.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER), Qualifier]
final class RequestHeaders
{
    public function __construct(public string $value)
    {
    }
}
