<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Exceptions\InvalidLogTargetException;
use CommonPHP\Logging\Exceptions\LogFormatterException;
use CommonPHP\Logging\Exceptions\UnwritableLogFileException;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\Tests\Fixtures\PlainFormatter;
use CommonPHP\Logging\Tests\Fixtures\TemporaryDirectoryTrait;
use CommonPHP\Logging\Tests\Fixtures\ThrowingFormatter;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class LogTargetTest extends TestCase
{
    use TemporaryDirectoryTrait;

    public function testFactoryTargetsExposeNamesAndSeverityHandling(): void
    {
        $target = LogTarget::callback(static fn (): null => null, 'important', minimumLevel: 'warning');

        self::assertSame('important', $target->getName());
        self::assertFalse($target->handles(new LogRecord('debug', 'Debug')));
        self::assertTrue($target->handles(new LogRecord('warning', 'Warning')));
        self::assertTrue($target->handles(new LogRecord('emergency', 'Emergency')));
    }

    public function testExactLevelsOverrideMinimumSeverity(): void
    {
        $target = LogTarget::callback(
            static fn (): null => null,
            'exact',
            minimumLevel: 'debug',
            levels: ['notice', 'error'],
        );

        self::assertFalse($target->handles(new LogRecord('debug', 'Debug')));
        self::assertTrue($target->handles(new LogRecord('notice', 'Notice')));
        self::assertFalse($target->handles(new LogRecord('warning', 'Warning')));
        self::assertTrue($target->handles(new LogRecord('error', 'Error')));
    }

    public function testCallbackTargetReceivesRecordAndFormattedLine(): void
    {
        $received = [];
        $target = LogTarget::callback(
            static function (LogRecord $record, string $line) use (&$received): void {
                $received[] = [$record, $line];
            },
            formatter: new PlainFormatter(),
        );
        $record = new LogRecord('info', 'Callback line');

        $target->write($record);

        self::assertCount(1, $received);
        self::assertSame($record, $received[0][0]);
        self::assertSame('Callback line' . PHP_EOL, $received[0][1]);
    }

    public function testUnhandledRecordsAreNotWritten(): void
    {
        $calls = 0;
        $target = LogTarget::callback(
            static function () use (&$calls): void {
                ++$calls;
            },
            minimumLevel: 'emergency',
        );

        $target->write(new LogRecord('debug', 'Ignored'));

        self::assertSame(0, $calls);
    }

    public function testStreamTargetWritesToDestinationAndCanSkipNewLine(): void
    {
        $directory = $this->createTemporaryDirectory();
        $path = $directory . DIRECTORY_SEPARATOR . 'stream.log';
        $target = new LogTarget('stream', $path, new PlainFormatter(), appendNewLine: false);

        $target->write(new LogRecord('info', 'Stream line'));

        self::assertSame('Stream line', file_get_contents($path));
    }

    public function testFileTargetCreatesDirectoriesAndAppendsLines(): void
    {
        $directory = $this->createTemporaryDirectory();
        $path = $directory . DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'app.log';
        $target = LogTarget::file($path, formatter: new PlainFormatter());

        $target->write(new LogRecord('info', 'First'));
        $target->write(new LogRecord('error', 'Second'));

        self::assertSame('First' . PHP_EOL . 'Second' . PHP_EOL, file_get_contents($path));
    }

    public function testErrorLogTargetWritesToConfiguredPhpErrorLog(): void
    {
        $directory = $this->createTemporaryDirectory();
        $path = $directory . DIRECTORY_SEPARATOR . 'php-error.log';
        $previousErrorLog = ini_get('error_log');
        $previousLogErrors = ini_get('log_errors');

        ini_set('error_log', $path);
        ini_set('log_errors', '1');

        try {
            LogTarget::errorLog(formatter: new PlainFormatter())->write(new LogRecord('error', 'Error log line'));
        } finally {
            ini_set('error_log', (string) $previousErrorLog);
            ini_set('log_errors', (string) $previousLogErrors);
        }

        self::assertStringContainsString('Error log line', (string) file_get_contents($path));
    }

    public function testInvalidTargetNameIsRejected(): void
    {
        $this->expectException(InvalidLogTargetException::class);
        $this->expectExceptionMessage('Log target names cannot be empty.');

        new LogTarget('', 'php://memory');
    }

    public function testInvalidMinimumLevelIsRejected(): void
    {
        $this->expectException(InvalidLogTargetException::class);

        new LogTarget('bad-level', 'php://memory', minimumLevel: 'verbose');
    }

    public function testInvalidExactLevelIsRejected(): void
    {
        $this->expectException(InvalidLogTargetException::class);

        new LogTarget('bad-exact-level', 'php://memory', levels: ['verbose']);
    }

    public function testStreamTargetWithoutDestinationThrows(): void
    {
        $target = new LogTarget('missing-stream', '', new PlainFormatter());

        $this->expectException(InvalidLogTargetException::class);
        $this->expectExceptionMessage('has no destination');

        $target->write(new LogRecord('info', 'No destination'));
    }

    public function testFileTargetWithoutPathThrows(): void
    {
        $target = LogTarget::file('', formatter: new PlainFormatter());

        $this->expectException(InvalidLogTargetException::class);
        $this->expectExceptionMessage('has no path');

        $target->write(new LogRecord('info', 'No path'));
    }

    public function testFileTargetRejectsDirectoryPath(): void
    {
        $directory = $this->createTemporaryDirectory();
        $target = LogTarget::file($directory, formatter: new PlainFormatter());

        $this->expectException(UnwritableLogFileException::class);
        $this->expectExceptionMessage('is a directory');

        $target->write(new LogRecord('info', 'Directory path'));
    }

    public function testFormatterRuntimeFailuresAreWrapped(): void
    {
        $target = LogTarget::callback(
            static fn (): null => null,
            formatter: new ThrowingFormatter(new RuntimeException('Boom')),
        );

        try {
            $target->write(new LogRecord('info', 'Bad formatter'));
            self::fail('Expected formatter failure.');
        } catch (LogFormatterException $exception) {
            self::assertStringContainsString('Failed to format log record', $exception->getMessage());
            self::assertInstanceOf(RuntimeException::class, $exception->getPrevious());
        }
    }

    public function testFormatterLoggingFailuresPassThrough(): void
    {
        $expected = new LogFormatterException('Known formatter failure.');
        $target = LogTarget::callback(
            static fn (): null => null,
            formatter: new ThrowingFormatter($expected),
        );

        try {
            $target->write(new LogRecord('info', 'Bad formatter'));
            self::fail('Expected formatter failure.');
        } catch (LogFormatterException $exception) {
            self::assertSame($expected, $exception);
        }
    }
}
