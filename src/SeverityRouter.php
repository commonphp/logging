<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\Exceptions\InvalidLogTargetException;
use CommonPHP\Logging\Exceptions\LogTargetException;
use Throwable;

final class SeverityRouter
{
    /**
     * @var array<string, LogTargetInterface>
     */
    private array $targets = [];

    /**
     * @param iterable<LogTargetInterface> $targets
     */
    public function __construct(iterable $targets = [])
    {
        foreach ($targets as $target) {
            $this->addTarget($target);
        }
    }

    public function addTarget(LogTargetInterface $target): self
    {
        $name = $target->getName();

        if ($name === '') {
            throw new InvalidLogTargetException('Log target names cannot be empty.');
        }

        if (isset($this->targets[$name])) {
            throw new InvalidLogTargetException('Log target "' . $name . '" is already registered.');
        }

        $this->targets[$name] = $target;

        return $this;
    }

    public function removeTarget(string $name): self
    {
        unset($this->targets[$name]);

        return $this;
    }

    public function hasTarget(string $name): bool
    {
        return isset($this->targets[$name]);
    }

    public function getTarget(string $name): LogTargetInterface
    {
        return $this->targets[$name]
            ?? throw new InvalidLogTargetException('Log target "' . $name . '" is not registered.');
    }

    /**
     * @return array<string, LogTargetInterface>
     */
    public function getTargets(): array
    {
        return $this->targets;
    }

    public function isEmpty(): bool
    {
        return $this->targets === [];
    }

    public function dispatch(LogRecord $record): void
    {
        foreach ($this->targets as $target) {
            if (!$target->handles($record)) {
                continue;
            }

            try {
                $target->write($record);
            } catch (LogTargetException $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                throw new LogTargetException(
                    'Log target "' . $target->getName() . '" failed: ' . $exception->getMessage(),
                    $exception->getCode(),
                    $exception,
                );
            }
        }
    }
}
