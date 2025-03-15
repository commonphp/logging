<?php

namespace Neuron\Logging;

use Psr\Log\LoggerInterface;

/**
 * Interface LogHandlerInterface
 *
 * Defines the contract for a log handler.
 */
interface LogHandlerInterface extends LoggerInterface
{
    /**
     * Set up a handler for logging
     *
     * @param array $options The options to pass to the handler
     * @return void
     */
    public function setup(array $options): void;

    /**
     * Check to see if the log handler supports the specified log level
     *
     * @param string $level The level to check
     * @return bool
     */
    public function supports(string $level): bool;
}