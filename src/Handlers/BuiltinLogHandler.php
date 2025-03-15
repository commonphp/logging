<?php

/** @noinspection PhpUnusedLocalVariableInspection, PhpUnused */

namespace Neuron\Logging\Handlers;

use Neuron\Logging\LogHandlerInterface;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Class BuiltinLogHandler
 *
 * A built-in log handler for managing log messages.
 */
class BuiltinLogHandler extends AbstractLogger implements LogHandlerInterface
{
    /** @inheritDoc */
    public function setup(array $options): void {} // Builtin logger has not setup

    /** @inheritDoc */
    public function supports(string $level): bool { return true; } // Support all log levels

    /**
     * Logs a message with the given level.
     *
     * @param string $level The severity level of the log.
     * @param Stringable|string $message The message to log.
     * @param array $context Optional array of context data.
     * @return void
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $count = 1;
        $content = str_replace('] ', '] '.strtoupper($level).' ', $message, $count);
        error_log($content);
    }
}