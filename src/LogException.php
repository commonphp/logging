<?php

namespace Neuron\Logging;

use Exception;
use Throwable;

/**
 * Class LogException
 *
 * General exception class for logging-related errors.
 */
class LogException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}