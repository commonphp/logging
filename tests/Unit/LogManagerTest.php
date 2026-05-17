<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Exceptions\LogDriverException;
use CommonPHP\Logging\LogManager;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\Tests\Fixtures\ArrayLogDriver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LogManagerTest extends TestCase
{
    public function testItImplementsPsrLoggerAndDelegatesToDriver(): void
    {
        $driver = new ArrayLogDriver();
        $manager = new LogManager($driver);

        self::assertInstanceOf(LoggerInterface::class, $manager);

        $manager->error('Failed', ['id' => 1]);

        self::assertCount(1, $driver->records);
        self::assertSame('error', $driver->records[0]->level->value);
        self::assertSame('Failed', $driver->records[0]->message);
        self::assertSame(['id' => 1], $driver->records[0]->context);
    }

    public function testItCanSwapDrivers(): void
    {
        $first = new ArrayLogDriver('first');
        $second = new ArrayLogDriver('second');
        $manager = new LogManager($first);

        self::assertSame($first, $manager->getDriver());
        self::assertSame($manager, $manager->useDriver($second));
        self::assertSame($second, $manager->getDriver());

        $manager->info('Swapped');

        self::assertSame([], $first->records);
        self::assertSame('second', $second->records[0]->channel);
    }

    public function testItCanAddTargetsAndProcessorsToNativeDrivers(): void
    {
        $received = [];
        $manager = new LogManager(new NativeLogDriver(targets: []));
        $manager
            ->addTarget(LogTarget::callback(static function (LogRecord $record) use (&$received): void {
                $received[] = $record;
            }, 'capture'))
            ->pushProcessor(new class implements LogProcessorInterface {
                public function process(LogRecord $record): LogRecord
                {
                    return $record->withMessage('Managed ' . $record->message);
                }
            });

        $manager->warning('message');

        self::assertSame('Managed message', $received[0]->message);
    }

    public function testAddingTargetsToUnsupportedDriversThrows(): void
    {
        $manager = new LogManager(new ArrayLogDriver());

        $this->expectException(LogDriverException::class);
        $this->expectExceptionMessage('does not support adding targets');

        $manager->addTarget(LogTarget::callback(static fn (): null => null));
    }

    public function testAddingProcessorsToUnsupportedDriversThrows(): void
    {
        $manager = new LogManager(new ArrayLogDriver());

        $this->expectException(LogDriverException::class);
        $this->expectExceptionMessage('does not support adding processors');

        $manager->pushProcessor(new class implements LogProcessorInterface {
            public function process(LogRecord $record): LogRecord
            {
                return $record;
            }
        });
    }
}
