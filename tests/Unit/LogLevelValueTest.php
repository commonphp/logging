<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Enums\LogLevelValue;
use CommonPHP\Logging\Tests\Fixtures\StringableValue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LogLevelValueTest extends TestCase
{
    public function testItListsPsrLogLevelValuesInSeverityOrder(): void
    {
        self::assertSame(
            ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'],
            LogLevelValue::values(),
        );
    }

    public function testItNormalizesStringsStringablesAndExistingCases(): void
    {
        self::assertSame(LogLevelValue::Warning, LogLevelValue::fromLevel(' WARNING '));
        self::assertSame(LogLevelValue::Error, LogLevelValue::fromLevel(new StringableValue('error')));
        self::assertSame(LogLevelValue::Alert, LogLevelValue::fromLevel(LogLevelValue::Alert));
    }

    public function testItReportsSeverityAndMinimumComparisons(): void
    {
        self::assertSame(100, LogLevelValue::Debug->severity());
        self::assertSame(400, LogLevelValue::Error->severity());
        self::assertSame(600, LogLevelValue::Emergency->severity());
        self::assertTrue(LogLevelValue::Critical->isAtLeast(LogLevelValue::Error));
        self::assertFalse(LogLevelValue::Info->isAtLeast(LogLevelValue::Warning));
    }

    public function testItRejectsUnknownLevels(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported log level "verbose".');

        LogLevelValue::fromLevel('verbose');
    }
}
