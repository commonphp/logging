<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use CommonPHP\Logging\Contracts\AbstractLogDriver;
use CommonPHP\Logging\LogRecord;
use Stringable;

final class ArrayLogDriver extends AbstractLogDriver
{
    /**
     * @var list<LogRecord>
     */
    public array $records = [];

    public function __construct(
        private readonly string $channel = 'array',
    ) {
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->records[] = $this->newRecord($message, $level, $context, $this->channel);
    }
}
