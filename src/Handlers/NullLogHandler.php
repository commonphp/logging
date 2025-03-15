<?php

/** @noinspection PhpUnusedLocalVariableInspection, PhpUnused */

namespace Neuron\Logging\Handlers;

use Neuron\Logging\LogHandlerInterface;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Class NullLogHandler
 *
 * A log handler that discards all log messages.
 */
class NullLogHandler extends AbstractLogger implements LogHandlerInterface
{
    /** @inheritDoc */
    public function setup(array $options): void {} // Builtin logger has not setup

    /** @inheritDoc */
    public function supports(string $level): bool { return true; } // Support all log levels

    /**
     * Logs a message by doing nothing.
     *
     * @param string $level The severity level of the log.
     * @param Stringable|string $message The message to log.
     * @param array $context Optional array of context data.
     * @return void
     */
    public function log($level, Stringable|string $message, array $context = []): void {} // Do nothing
}