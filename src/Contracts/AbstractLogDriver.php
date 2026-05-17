<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use Psr\Log\AbstractLogger;

abstract class AbstractLogDriver extends AbstractLogger implements LogDriverInterface
{

    public function getName(): string
    {
        return static::class;
    }
}