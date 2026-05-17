<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Exceptions\InvalidLogTargetException;
use CommonPHP\Logging\Exceptions\LogDriverException;
use CommonPHP\Logging\Exceptions\LogFormatterException;
use CommonPHP\Logging\Exceptions\LoggingException;
use CommonPHP\Logging\Exceptions\LogProcessorException;
use CommonPHP\Logging\Exceptions\LogTargetException;
use CommonPHP\Logging\Exceptions\UnwritableLogFileException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionsTest extends TestCase
{
    public function testLoggingExceptionsShareABaseType(): void
    {
        self::assertInstanceOf(RuntimeException::class, new LoggingException());
        self::assertInstanceOf(LoggingException::class, new LogDriverException());
        self::assertInstanceOf(LoggingException::class, new LogFormatterException());
        self::assertInstanceOf(LoggingException::class, new LogProcessorException());
        self::assertInstanceOf(LoggingException::class, new LogTargetException());
    }

    public function testTargetSpecificExceptionsExtendTargetException(): void
    {
        self::assertInstanceOf(LogTargetException::class, new InvalidLogTargetException());
        self::assertInstanceOf(LogTargetException::class, new UnwritableLogFileException());
    }
}
