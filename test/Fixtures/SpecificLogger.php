<?php

namespace NeuronTests\Logging\Fixtures;


/**
 * A logger that only supports a specific log level.
 */
class SpecificLogger extends DummyLogHandler
{
    private string $supportedLevel;

    public function setup(array $options): void
    {
        $this->supportedLevel = $options['level'] ?? 'debug';
        parent::setup($options);
    }

    public function supports(string $level): bool
    {
        return $level === $this->supportedLevel;
    }
}