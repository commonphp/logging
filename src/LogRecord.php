<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Logging\Enums\LogLevelValue;
use DateTimeImmutable;
use DateTimeInterface;
use Stringable;

final readonly class LogRecord
{
    public DateTimeImmutable $timestamp;

    public LogLevelValue $level;

    public string $message;

    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $extra
     */
    public function __construct(
        LogLevelValue|Stringable|string $level,
        Stringable|string $message,
        public array $context = [],
        ?DateTimeImmutable $timestamp = null,
        public string $channel = 'app',
        public array $extra = [],
    ) {
        $this->timestamp = $timestamp ?? new DateTimeImmutable();
        $this->level = LogLevelValue::fromLevel($level);
        $this->message = (string) $message;
    }

    public function withMessage(Stringable|string $message): self
    {
        return new self(
            $this->level,
            $message,
            $this->context,
            $this->timestamp,
            $this->channel,
            $this->extra,
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    public function withContext(array $context): self
    {
        return new self(
            $this->level,
            $this->message,
            $context,
            $this->timestamp,
            $this->channel,
            $this->extra,
        );
    }

    /**
     * @param array<string, mixed> $extra
     */
    public function withExtra(array $extra): self
    {
        return new self(
            $this->level,
            $this->message,
            $this->context,
            $this->timestamp,
            $this->channel,
            $extra,
        );
    }

    public function withChannel(string $channel): self
    {
        return new self(
            $this->level,
            $this->message,
            $this->context,
            $this->timestamp,
            $channel,
            $this->extra,
        );
    }

    public function withExtraValue(string $key, mixed $value): self
    {
        return $this->withExtra([$key => $value] + $this->extra);
    }

    /**
     * @return array{
     *     timestamp: string,
     *     level: string,
     *     message: string,
     *     context: array<string, mixed>,
     *     channel: string,
     *     extra: array<string, mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->format(DateTimeInterface::ATOM),
            'level' => $this->level->value,
            'message' => $this->message,
            'context' => $this->context,
            'channel' => $this->channel,
            'extra' => $this->extra,
        ];
    }
}
