<?php

namespace NeuronTests\Logging\Fixtures;

use Psr\Container\ContainerInterface;

class DummyContainer implements ContainerInterface
{
    public function get(string $id)
    {
        return new $id();
    }

    public function has(string $id): bool
    {
        return true;
    }
}