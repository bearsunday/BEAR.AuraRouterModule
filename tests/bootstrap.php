<?php

declare(strict_types=1);

use Koriym\Attributes\AttributeReader;
use Ray\ServiceLocator\ServiceLocator;

// no annotation in PHP 8
if (PHP_MAJOR_VERSION >= 8) {
    ServiceLocator::setReader(new AttributeReader());
}
