<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Drivers;

use CommonPHP\Logging\Contracts\AbstractLogDriver;

class NativeLogDriver extends AbstractLogDriver
{

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }
}