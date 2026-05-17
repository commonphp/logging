<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use Psr\Log\AbstractLogger;

final class LogManager extends AbstractLogger
{

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }
}