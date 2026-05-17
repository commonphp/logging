<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use CommonPHP\Logging\LogRecord;

interface LogFormatterInterface
{
    public function format(LogRecord $record): string;
}
