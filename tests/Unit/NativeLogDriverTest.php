<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Exceptions\LogProcessorException;
use CommonPHP\Logging\Exceptions\LogTargetException;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\Tests\Fixtures\CapturingTarget;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use RuntimeException;

final class NativeLogDriverTest extends TestCase
{
    public function testDefaultDriverUsesErrorLogTarget(): void
    {
        $driver = new NativeLogDriver();

        self::assertTrue($driver->getRouter()->hasTarget('error_log'));
    }

    public function testItLogsThroughProcessorsAndTargets(): void
    {
        $received = [];
        $driver = new NativeLogDriver(
            targets: [
                LogTarget::callback(static function (LogRecord $record, string $line) use (&$received): void {
                    $received[] = [$record, $line];
                }),
            ],
            channel: 'billing',
        );

        $driver->info('Invoice {number} paid', ['number' => 'INV-1']);

        self::assertCount(1, $received);
        self::assertSame('billing', $received[0][0]->channel);
        self::assertSame('Invoice INV-1 paid', $received[0][0]->message);
        self::assertStringContainsString('billing.info: Invoice INV-1 paid', $received[0][1]);
    }

    public function testItCanDisableDefaultInterpolationWithExplicitProcessors(): void
    {
        $received = [];
        $driver = new NativeLogDriver(
            targets: [
                LogTarget::callback(static function (LogRecord $record) use (&$received): void {
                    $received[] = $record;
                }),
            ],
            processors: [],
        );

        $driver->info('Hello {name}', ['name' => 'Ada']);

        self::assertSame('Hello {name}', $received[0]->message);
    }

    public function testCustomProcessorsCanMutateRecords(): void
    {
        $received = [];
        $processor = new class implements LogProcessorInterface {
            public function process(LogRecord $record): LogRecord
            {
                return $record
                    ->withMessage('Processed: ' . $record->message)
                    ->withExtraValue('processed', true);
            }
        };
        $driver = new NativeLogDriver(
            targets: [
                LogTarget::callback(static function (LogRecord $record) use (&$received): void {
                    $received[] = $record;
                }),
            ],
            processors: [$processor],
        );

        $driver->notice('Original');

        self::assertSame('Processed: Original', $received[0]->message);
        self::assertSame(['processed' => true], $received[0]->extra);
    }

    public function testTargetsCanBeAddedAndRemoved(): void
    {
        $received = [];
        $driver = new NativeLogDriver(targets: []);
        $target = LogTarget::callback(static function (LogRecord $record) use (&$received): void {
            $received[] = $record;
        }, 'capture');

        $driver->addTarget($target);
        self::assertTrue($driver->getRouter()->hasTarget('capture'));

        $driver->debug('Captured');
        $driver->removeTarget('capture');
        $driver->debug('Ignored');

        self::assertCount(1, $received);
        self::assertSame('Captured', $received[0]->message);
        self::assertFalse($driver->getRouter()->hasTarget('capture'));
    }

    public function testInvalidLevelsRaisePsrExceptions(): void
    {
        $driver = new NativeLogDriver(targets: []);

        $this->expectException(InvalidArgumentException::class);

        $driver->log('verbose', 'Invalid');
    }

    public function testProcessorRuntimeFailuresAreWrapped(): void
    {
        $processor = new class implements LogProcessorInterface {
            public function process(LogRecord $record): LogRecord
            {
                throw new RuntimeException('Processor broke');
            }
        };
        $driver = new NativeLogDriver(targets: [], processors: [$processor]);

        try {
            $driver->info('Failure');
            self::fail('Expected processor failure.');
        } catch (LogProcessorException $exception) {
            self::assertStringContainsString('Log processor', $exception->getMessage());
            self::assertInstanceOf(RuntimeException::class, $exception->getPrevious());
        }
    }

    public function testProcessorLoggingFailuresPassThrough(): void
    {
        $expected = new LogProcessorException('Known processor failure.');
        $processor = new class($expected) implements LogProcessorInterface {
            public function __construct(private readonly LogProcessorException $exception)
            {
            }

            public function process(LogRecord $record): LogRecord
            {
                throw $this->exception;
            }
        };
        $driver = new NativeLogDriver(targets: [], processors: [$processor]);

        try {
            $driver->info('Failure');
            self::fail('Expected processor failure.');
        } catch (LogProcessorException $exception) {
            self::assertSame($expected, $exception);
        }
    }

    public function testInvalidProcessorReturnTypesAreWrapped(): void
    {
        $processor = new class implements LogProcessorInterface {
            public function process(LogRecord $record): LogRecord
            {
                return null;
            }
        };
        $driver = new NativeLogDriver(targets: [], processors: [$processor]);

        $this->expectException(LogProcessorException::class);
        $this->expectExceptionMessage('Log processor');

        $driver->info('Failure');
    }

    public function testTargetFailuresPassThroughAsLoggingExceptions(): void
    {
        $driver = new NativeLogDriver(targets: [
            new CapturingTarget('bad', true, new RuntimeException('Target broke')),
        ]);

        $this->expectException(LogTargetException::class);
        $this->expectExceptionMessage('Log target "bad" failed');

        $driver->info('Failure');
    }
}
