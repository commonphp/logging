<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use CommonPHP\Logging\LogRecord;

interface LogProcessorInterface
{
    public function process(LogRecord $record): LogRecord;
}
