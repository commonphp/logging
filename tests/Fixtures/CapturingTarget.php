<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\LogRecord;
use Throwable;

final class CapturingTarget implements LogTargetInterface
{
    /**
     * @var list<LogRecord>
     */
    public array $records = [];

    public function __construct(
        private readonly string $name = 'capture',
        private readonly bool $handles = true,
        private readonly ?Throwable $throwable = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function handles(LogRecord $record): bool
    {
        return $this->handles;
    }

    public function write(LogRecord $record): void
    {
        if ($this->throwable !== null) {
            throw $this->throwable;
        }

        $this->records[] = $record;
    }
}
