<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Router\Annotation;

use Attribute;
use Ray\Di\Di\Qualifier;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER), Qualifier]
final class RequestHeaders
{
    public function __construct(public string $value)
    {
    }
}
