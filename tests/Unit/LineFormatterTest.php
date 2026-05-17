<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Exceptions\LogFormatterException;
use CommonPHP\Logging\Formatters\LineFormatter;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\Tests\Fixtures\StringableValue;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class LineFormatterTest extends TestCase
{
    public function testItFormatsBasicRecordLines(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17 12:00:00', new DateTimeZone('UTC'));
        $record = new LogRecord('info', 'Created', timestamp: $timestamp, channel: 'billing');

        self::assertSame(
            '[2026-05-17T12:00:00+00:00] billing.info: Created',
            (new LineFormatter())->format($record),
        );
    }

    public function testItUsesCustomDateFormatAndCanOmitContextAndExtra(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17 12:00:00', new DateTimeZone('UTC'));
        $record = new LogRecord('warning', 'Compact', ['id' => 1], $timestamp, 'app', ['request_id' => 'abc']);
        $formatter = new LineFormatter('Y-m-d H:i:s', includeContext: false, includeExtra: false);

        self::assertSame('[2026-05-17 12:00:00] app.warning: Compact', $formatter->format($record));
    }

    public function testItNormalizesContextAndExtraValues(): void
    {
        $resource = fopen('php://memory', 'r');
        self::assertIsResource($resource);

        try {
            $timestamp = new DateTimeImmutable('2026-05-17 12:00:00', new DateTimeZone('UTC'));
            $record = new LogRecord('error', 'Rich', [
                'date' => $timestamp,
                'throwable' => new RuntimeException('Nope', 9),
                'stringable' => new StringableValue('value'),
                'object' => new stdClass(),
                'resource' => $resource,
                'deep' => ['a' => ['b' => ['c' => ['d' => ['e' => ['f' => 'too deep']]]]]],
            ], $timestamp, 'app', ['extra' => new StringableValue('metadata')]);

            $line = (new LineFormatter())->format($record);
        } finally {
            fclose($resource);
        }

        self::assertStringContainsString('"date":"2026-05-17T12:00:00+00:00"', $line);
        self::assertStringContainsString('"throwable":{"class":"RuntimeException","message":"Nope","code":9', $line);
        self::assertStringContainsString('"stringable":"value"', $line);
        self::assertStringContainsString('"object":{"class":"stdClass"}', $line);
        self::assertStringContainsString('"resource":"[stream resource]"', $line);
        self::assertStringContainsString('"[max-depth]"', $line);
        self::assertStringContainsString('"extra":"metadata"', $line);
    }

    public function testItThrowsFormatterExceptionWhenJsonEncodingFails(): void
    {
        $record = new LogRecord('info', 'Invalid UTF-8', ['bad' => "\xB1"]);

        $this->expectException(LogFormatterException::class);
        $this->expectExceptionMessage('Failed to encode log context');

        (new LineFormatter())->format($record);
    }
}
