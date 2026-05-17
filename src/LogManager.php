<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Logging\Contracts\LogDriverInterface;
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Exceptions\LogDriverException;
use Psr\Log\AbstractLogger;
use Stringable;

final class LogManager extends AbstractLogger
{
    public function __construct(
        private LogDriverInterface $driver = new NativeLogDriver(),
    ) {
    }

    public function useDriver(LogDriverInterface $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver(): LogDriverInterface
    {
        return $this->driver;
    }

    public function addTarget(LogTargetInterface $target): self
    {
        if (!method_exists($this->driver, 'addTarget')) {
            throw new LogDriverException('The current log driver does not support adding targets.');
        }

        $this->driver->addTarget($target);

        return $this;
    }

    public function pushProcessor(LogProcessorInterface $processor): self
    {
        if (!method_exists($this->driver, 'pushProcessor')) {
            throw new LogDriverException('The current log driver does not support adding processors.');
        }

        $this->driver->pushProcessor($processor);

        return $this;
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->driver->log($level, $message, $context);
    }
}
