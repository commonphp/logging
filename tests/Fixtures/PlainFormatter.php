<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\LogRecord;

final class PlainFormatter implements LogFormatterInterface
{
    public function format(LogRecord $record): string
    {
        return $record->message;
    }
}
