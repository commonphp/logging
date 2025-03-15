<?php

use Neuron\Logging\LogHandlerInterface;
use Neuron\Logging\LogInterface;
use Psr\Container\ContainerInterface;

// Provides services for the log interface or classes that implement LogInterface
class LogExampleContainer implements ContainerInterface
{
    private LogInterface $log;
    private array $items = [];

    public function setLog(LogInterface $log): void
    {
        $this->log = $log;
    }

    public function get(string $id)
    {
        if ($id == LogInterface::class) {
            return $this->log;
        } if (!isset($this->items[$id])) {
            if (is_subclass_of($id, LogHandlerInterface::class)) {
                if (isset($this->log)) {
                    $this->items[$id] = new $id($this->log);
                } else {
                    return new $id();
                }
            } else {
                return null;
            }
        }
        return $this->items[$id];
    }

    public function has(string $id): bool
    {
        return $id == LogInterface::class || isset($this->items[$id]) || is_subclass_of($id, LogHandlerInterface::class);
    }
}