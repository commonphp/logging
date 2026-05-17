<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Processors;

use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\LogRecord;
use DateTimeInterface;
use Stringable;

final class InterpolationProcessor implements LogProcessorInterface
{
    public function process(LogRecord $record): LogRecord
    {
        if (!str_contains($record->message, '{')) {
            return $record;
        }

        $replace = [];

        foreach ($record->context as $key => $value) {
            $replacement = $this->stringify($value);

            if ($replacement !== null) {
                $replace['{' . $key . '}'] = $replacement;
            }
        }

        if ($replace === []) {
            return $record;
        }

        return $record->withMessage(strtr($record->message, $replace));
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        return null;
    }
}
