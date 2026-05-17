<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Enums;

use InvalidArgumentException;
use Stringable;

enum LogLevelValue: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Notice = 'notice';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';
    case Alert = 'alert';
    case Emergency = 'emergency';

    public static function fromLevel(self|Stringable|string $level): self
    {
        if ($level instanceof self) {
            return $level;
        }

        $value = strtolower(trim((string) $level));

        return self::tryFrom($value)
            ?? throw new InvalidArgumentException('Unsupported log level "' . $value . '".');
    }

    public function severity(): int
    {
        return match ($this) {
            self::Debug => 100,
            self::Info => 200,
            self::Notice => 250,
            self::Warning => 300,
            self::Error => 400,
            self::Critical => 500,
            self::Alert => 550,
            self::Emergency => 600,
        };
    }

    public function isAtLeast(self $minimum): bool
    {
        return $this->severity() >= $minimum->severity();
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $level): string => $level->value,
            self::cases(),
        );
    }
}
