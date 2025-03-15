<?php

namespace Neuron\Logging\Exceptions;

use Neuron\Logging\LogException;
use Throwable;

/**
 * Class UndefinedLogLevelException
 *
 * Exception thrown when an undefined or invalid log level is used.
 */
class UndefinedLogLevelException extends LogException
{
    public function __construct(int|string $level, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The log level `'.$level.'` is not defined.', $code, $previous);
    }
}