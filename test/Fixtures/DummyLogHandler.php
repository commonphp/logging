<?php

namespace NeuronTests\Logging\Fixtures;
use Neuron\Logging\LogHandlerInterface;
use Psr\Log\LogLevel;

/**
 * A simple dummy logger for testing purposes.
 */
class DummyLogHandler implements LogHandlerInterface
{
    public array $messages = [];
    protected array $options = [];

    public function setup(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Determines whether the logger supports the given log level.
     * If a 'supported' option is provided, only those levels will be accepted.
     */
    public function supports(string $level): bool
    {
        if (isset($this->options['supported'])) {
            return in_array($level, $this->options['supported'], true);
        }
        return true;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->messages[] = [
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];
    }
}