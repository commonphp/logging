<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Enums\LogLevelValue;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\Tests\Fixtures\StringableValue;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

final class LogRecordTest extends TestCase
{
    public function testItNormalizesCoreValuesAndProvidesDefaults(): void
    {
        $record = new LogRecord('INFO', new StringableValue('Created'), ['id' => 12]);

        self::assertSame(LogLevelValue::Info, $record->level);
        self::assertSame('Created', $record->message);
        self::assertSame(['id' => 12], $record->context);
        self::assertSame('app', $record->channel);
        self::assertSame([], $record->extra);
    }

    public function testItUsesProvidedTimestampChannelAndExtra(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17T12:00:00+00:00');
        $record = new LogRecord(
            LogLevelValue::Notice,
            'Queued',
            ['job' => 'invoice'],
            $timestamp,
            'billing',
            ['request_id' => 'abc'],
        );

        self::assertSame($timestamp, $record->timestamp);
        self::assertSame('billing', $record->channel);
        self::assertSame(['request_id' => 'abc'], $record->extra);
    }

    public function testWithMethodsReturnChangedCopies(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17T12:00:00+00:00');
        $record = new LogRecord('warning', 'Original', ['a' => 1], $timestamp, 'app', ['first' => true]);

        $message = $record->withMessage('Changed');
        $context = $record->withContext(['b' => 2]);
        $extra = $record->withExtra(['second' => true]);
        $channel = $record->withChannel('audit');
        $extraValue = $record->withExtraValue('request_id', 'abc');

        self::assertNotSame($record, $message);
        self::assertSame('Changed', $message->message);
        self::assertSame(['b' => 2], $context->context);
        self::assertSame(['second' => true], $extra->extra);
        self::assertSame('audit', $channel->channel);
        self::assertSame(['request_id' => 'abc', 'first' => true], $extraValue->extra);
        self::assertSame('Original', $record->message);
        self::assertSame(['a' => 1], $record->context);
        self::assertSame(['first' => true], $record->extra);
        self::assertSame($timestamp, $message->timestamp);
    }

    public function testItSerializesToArray(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17 07:30:00', new DateTimeZone('UTC'));
        $record = new LogRecord('error', 'Failed', ['id' => 1], $timestamp, 'api', ['retry' => false]);

        self::assertSame([
            'timestamp' => $timestamp->format(DateTimeInterface::ATOM),
            'level' => 'error',
            'message' => 'Failed',
            'context' => ['id' => 1],
            'channel' => 'api',
            'extra' => ['retry' => false],
        ], $record->toArray());
    }
}
