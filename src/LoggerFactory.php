<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Logging\Contracts\LogDriverInterface;
use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Enums\LogLevelValue;
use Stringable;

final class LoggerFactory
{
    public function create(?LogDriverInterface $driver = null): LogManager
    {
        return new LogManager($driver ?? new NativeLogDriver());
    }

    /**
     * @param iterable<LogTargetInterface>|null $targets
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function native(
        ?iterable $targets = null,
        ?iterable $processors = null,
        string $channel = 'app',
    ): LogManager {
        return $this->create(new NativeLogDriver(
            targets: $targets,
            processors: $processors,
            channel: $channel,
        ));
    }

    /**
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function file(
        string $path,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        ?LogFormatterInterface $formatter = null,
        ?iterable $processors = null,
        string $channel = 'app',
    ): LogManager {
        return $this->native(
            [LogTarget::file($path, formatter: $formatter, minimumLevel: $minimumLevel)],
            $processors,
            $channel,
        );
    }

    /**
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function stderr(
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        ?LogFormatterInterface $formatter = null,
        ?iterable $processors = null,
        string $channel = 'app',
    ): LogManager {
        return $this->native(
            [LogTarget::stderr(formatter: $formatter, minimumLevel: $minimumLevel)],
            $processors,
            $channel,
        );
    }

    /**
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function stdout(
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        ?LogFormatterInterface $formatter = null,
        ?iterable $processors = null,
        string $channel = 'app',
    ): LogManager {
        return $this->native(
            [LogTarget::stdout(formatter: $formatter, minimumLevel: $minimumLevel)],
            $processors,
            $channel,
        );
    }

    /**
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function errorLog(
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        ?LogFormatterInterface $formatter = null,
        ?iterable $processors = null,
        string $channel = 'app',
    ): LogManager {
        return $this->native(
            [LogTarget::errorLog(formatter: $formatter, minimumLevel: $minimumLevel)],
            $processors,
            $channel,
        );
    }
}
