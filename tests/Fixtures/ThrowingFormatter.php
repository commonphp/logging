<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\LogRecord;
use Throwable;

final readonly class ThrowingFormatter implements LogFormatterInterface
{
    public function __construct(
        private Throwable $throwable,
    ) {
    }

    public function format(LogRecord $record): string
    {
        throw $this->throwable;
    }
}
