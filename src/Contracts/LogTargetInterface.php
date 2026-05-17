<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use CommonPHP\Logging\LogRecord;

interface LogTargetInterface
{
    public function getName(): string;

    public function handles(LogRecord $record): bool;

    public function write(LogRecord $record): void;
}
