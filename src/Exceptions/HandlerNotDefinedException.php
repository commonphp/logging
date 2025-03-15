<?php

namespace Neuron\Logging\Exceptions;

use Neuron\Logging\LogException;
use Throwable;

/**
 * Class HandlerNotDefinedException
 *
 * Exception thrown when a requested log handler is not defined in the logging configuration.
 */
class HandlerNotDefinedException extends LogException
{
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The class `'.$class.'` is not defined.', $code, $previous);
    }
}