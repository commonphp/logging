<?php

namespace Neuron\Logging;

use Stringable;

/**
 * Interface LogInterface
 *
 * Defines the contract for logging operations.
 */
interface LogInterface
{
    /**
     * Get the current minimum logging level
     *
     * @return string
     */
    public function getLevel(): string;

    /**
     * SEt the minimum logging level
     *
     * @param string $level The logging level
     * @return void
     */
    public function setLevel(string $level): void;

    /**
     * Load a log handler into the manager
     *
     * @param class-string<LogHandlerInterface> $handlerClass The handler to add
     * @param array $options The options to provide to the handler
     * @param string ...$levels The supported log levels
     * @return LogHandlerInterface
     */
    public function load(string $handlerClass, array $options = [], string ... $levels): LogHandlerInterface;

    /**
     * Send a message to all log handlers
     *
     * @param mixed $level The level to log at
     * @param Stringable|string $message The message to send
     * @param array $context Any important context to provide
     * @return void
     */
    public function log($level, Stringable|string $message, array $context = []): void;
}