<?php

namespace Neuron\Logging\Exceptions;

use Neuron\Logging\LogException;
use Neuron\Logging\LogHandlerInterface;
use Throwable;

/**
 * Class HandlerMissingInterfaceException
 *
 * Exception thrown when a logging handler does not implement the required interface.
 */
class HandlerMissingInterfaceException extends LogException
{
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The class `'.$class.'` does not implement the '.LogHandlerInterface::class.' interface.', $code, $previous);
    }
}