<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use CommonPHP\Logging\Enums\LogLevelValue;
use CommonPHP\Logging\LogRecord;
use InvalidArgumentException;
use Psr\Log\InvalidArgumentException as PsrInvalidArgumentException;
use Psr\Log\AbstractLogger;
use Stringable;

abstract class AbstractLogDriver extends AbstractLogger implements LogDriverInterface
{

    public function getName(): string
    {
        return static::class;
    }

    protected function newRecord(
        Stringable|string $message,
        mixed $level,
        array $context = [],
        string $channel = 'app',
    ): LogRecord {
        if (!$level instanceof LogLevelValue && !$level instanceof Stringable && !is_string($level)) {
            throw new PsrInvalidArgumentException('Unsupported log level type "' . get_debug_type($level) . '".');
        }

        try {
            return new LogRecord($level, $message, $context, channel: $channel);
        } catch (InvalidArgumentException $exception) {
            throw new PsrInvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
