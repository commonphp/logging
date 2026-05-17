<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Formatters;

use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\Exceptions\LogFormatterException;
use CommonPHP\Logging\LogRecord;
use DateTimeInterface;
use JsonException;
use Stringable;
use Throwable;

final readonly class LineFormatter implements LogFormatterInterface
{
    public function __construct(
        private string $dateFormat = DateTimeInterface::ATOM,
        private bool $includeContext = true,
        private bool $includeExtra = true,
    ) {
    }

    public function format(LogRecord $record): string
    {
        $line = sprintf(
            '[%s] %s.%s: %s',
            $record->timestamp->format($this->dateFormat),
            $record->channel,
            $record->level->value,
            $record->message,
        );

        if ($this->includeContext && $record->context !== []) {
            $line .= ' ' . $this->encode(['context' => $record->context]);
        }

        if ($this->includeExtra && $record->extra !== []) {
            $line .= ' ' . $this->encode(['extra' => $record->extra]);
        }

        return $line;
    }

    /**
     * @param array<string, mixed> $values
     */
    private function encode(array $values): string
    {
        try {
            return json_encode(
                $this->normalizeValue($values),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            );
        } catch (JsonException $exception) {
            throw new LogFormatterException(
                'Failed to encode log context: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }
    }

    private function normalizeValue(mixed $value, int $depth = 0): mixed
    {
        if ($depth >= 6) {
            return '[max-depth]';
        }

        if ($value === null || is_scalar($value)) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if ($value instanceof Throwable) {
            return [
                'class' => $value::class,
                'message' => $value->getMessage(),
                'code' => $value->getCode(),
                'file' => $value->getFile(),
                'line' => $value->getLine(),
            ];
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeValue($item, $depth + 1);
            }

            return $normalized;
        }

        if (is_object($value)) {
            return ['class' => $value::class];
        }

        if (is_resource($value)) {
            return '[' . get_resource_type($value) . ' resource]';
        }

        return '[' . get_debug_type($value) . ']';
    }
}
