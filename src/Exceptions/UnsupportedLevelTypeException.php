<?php

namespace Neuron\Logging\Exceptions;

use Neuron\Logging\LogException;
use Neuron\Logging\LogLevelValues;
use Throwable;

/**
 * Class UnsupportedLevelTypeException
 *
 * Exception thrown when an unsupported type is used for a log level.
 */
class UnsupportedLevelTypeException extends LogException
{
    public function __construct(string $type, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The log level type `'.$type.'` is not support. Must be a string, integer, or instance of '.LogLevelValues::class, $code, $previous);
    }
}