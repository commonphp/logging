<?php

/** @noinspection PhpUnused */

namespace Neuron\Logging;

use Neuron\Logging\Exceptions\HandlerMissingInterfaceException;
use Neuron\Logging\Exceptions\HandlerNotDefinedException;
use Neuron\Logging\Exceptions\UndefinedLogLevelException;
use Neuron\Logging\Exceptions\UnsupportedLevelTypeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

/**
 * Class LogManager
 *
 * Manages log handlers and provides a unified interface to log messages.
 */
final class LogManager extends AbstractLogger implements LogInterface
{

    private ContainerInterface $container;

    /**
     * Array of registered log handlers.
     *
     * @var array<string, array<LogHandlerInterface>>
     */
    private array $handlers = [
        LogLevel::EMERGENCY => [],
        LogLevel::ALERT => [],
        LogLevel::CRITICAL => [],
        LogLevel::ERROR => [],
        LogLevel::WARNING => [],
        LogLevel::NOTICE => [],
        LogLevel::INFO => [],
        LogLevel::DEBUG => []
    ];

    private int $level;

    /**
     * Constructor
     *
     * @param ContainerInterface $container The PSR11 compatible container
     * @throws UnsupportedLevelTypeException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->setLevel(LogLevel::WARNING);
    }

    /**
     * Find the log level value from the provided name
     *
     * @param string $level The level name
     * @return int
     * @throws UndefinedLogLevelException
     */
    private function findLevelFromName(string $level): int
    {
        $level = strtolower($level);
        $levelValue = false;
        foreach (LogLEvelValues::cases() as $case) {
            if (strtolower($case->name) === $level) {
                $levelValue = $case->value;
                break;
            }
        }
        if ($levelValue === false) {
            throw new UndefinedLogLevelException($level);
        }
        return $levelValue;
    }

    /**
     * Find the log level name from the provided value
     *
     * @param int $level The level value
     * @return string
     * @throws UndefinedLogLevelException
     */
    private function findNameFromLevel(int $level): string
    {
        $levelValue = LogLevelValues::tryFrom($level);
        if ($levelValue === null) {
            throw new UndefinedLogLevelException($level);
        }
        return strtolower($levelValue->name);
    }

    /**
     * @inheritDoc
     * @throws UndefinedLogLevelException
     */
    public function getLevel(): string
    {
        return $this->findNameFromLevel($this->level);
    }

    /**
     * @inheritDoc
     * @throws UndefinedLogLevelException
     * @throws UnsupportedLevelTypeException
     */
    public function setLevel(string $level): void
    {
        $levelValue = $this->findLevelFromName($level);
        if ($levelValue < LogLevelValues::MIN_LEVEL || $levelValue > LogLevelValues::MAX_LEVEL) {
            throw new UnsupportedLevelTypeException($level);
        }
        $this->level = $levelValue;
    }

    /*
     * @param class-string<LogHandlerInterface> $handlerClass
     * @param array $options
     * @return void
     */

    /**
     * @inheritDoc
     * @throws HandlerMissingInterfaceException
     * @throws HandlerNotDefinedException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function load(string $handlerClass, array $options = [], string ... $levels): LogHandlerInterface
    {
        if (!class_exists($handlerClass)) {
            throw new HandlerNotDefinedException($handlerClass);
        }
        if (!is_subclass_of($handlerClass, LogHandlerInterface::class)) {
            throw new HandlerMissingInterfaceException($handlerClass);
        }
        /** @var LogHandlerInterface $handler */
        $handler = $this->container->get($handlerClass);
        $handler->setup($options);

        if (count($levels) == 0) {
            $levels = array_keys($this->handlers);
        }

        foreach ($levels as $level) {
            if ($handler->supports($level)) {
                $this->handlers[$level][] = $handler;
            }
        }

        return $handler;
    }

    /**
     * @inheritDoc
     * @throws UndefinedLogLevelException
     * @throws UnsupportedLevelTypeException
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        if ($level instanceof LogLevelValues) {
            $level = $level->name;
        } else if (is_int($level)) {
            $level = $this->findNameFromLevel($level);
        }

        if (!is_string($level)) {
            throw new UnsupportedLevelTypeException(is_object($level) ? get_class($level) : gettype($level));
        }

        $level = strtolower($level);

        if (!array_key_exists($level, $this->handlers)) {
            throw new UndefinedLogLevelException($level);
        }

        $levelValue = $this->findLevelFromName($level);

        if ($levelValue < 0 || $levelValue > $this->level) return; // Ignoring log level because it's out of range

        $outputCount = 0;

        foreach ($this->handlers[$level] as $handler) {
            $outputCount++;
            $handler->log($level, $message, $context);
        }

        if ($outputCount == 0)
        {
            error_log($message);
        }
    }
}