<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\Processors\InterpolationProcessor;
use CommonPHP\Logging\Tests\Fixtures\StringableValue;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use stdClass;

final class InterpolationProcessorTest extends TestCase
{
    public function testItInterpolatesSupportedContextValues(): void
    {
        $timestamp = new DateTimeImmutable('2026-05-17 12:00:00', new DateTimeZone('UTC'));
        $record = new LogRecord('info', 'Values {string} {int} {float} {true} {false} {null} {date} {object}', [
            'string' => 'alpha',
            'int' => 10,
            'float' => 1.25,
            'true' => true,
            'false' => false,
            'null' => null,
            'date' => $timestamp,
            'object' => new StringableValue('stringable'),
        ]);

        $processed = (new InterpolationProcessor())->process($record);

        self::assertSame(
            'Values alpha 10 1.25 true false null 2026-05-17T12:00:00+00:00 stringable',
            $processed->message,
        );
        self::assertSame($record->context, $processed->context);
    }

    public function testItLeavesUnsupportedContextPlaceholdersUntouched(): void
    {
        $record = new LogRecord('info', 'Complex {array} {object}', [
            'array' => ['a' => 1],
            'object' => new stdClass(),
        ]);

        $processed = (new InterpolationProcessor())->process($record);

        self::assertSame('Complex {array} {object}', $processed->message);
        self::assertSame($record, $processed);
    }

    public function testItReturnsOriginalRecordWhenNoInterpolationIsNeeded(): void
    {
        $record = new LogRecord('info', 'Nothing to replace', ['name' => 'Ada']);

        self::assertSame($record, (new InterpolationProcessor())->process($record));
    }
}
