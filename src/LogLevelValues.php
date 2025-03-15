<?php

namespace Neuron\Logging;

/**
 * Provides a numeric list of log level values
 */
enum LogLevelValues: int
{
    /** @var int The minimum log level value */
    public const int MIN_LEVEL = 0;

    /** @var int The maximum log level value */
    public const int MAX_LEVEL = 255;

    case EMERGENCY = 0;
    case ALERT = 25;
    case CRITICAL = 50;
    case ERROR = 75;
    case WARNING = 100;
    case NOTICE = 150;
    case INFO = 200;
    case DEBUG = 255;
}