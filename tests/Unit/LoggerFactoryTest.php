<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\LogManager;
use CommonPHP\Logging\LoggerFactory;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\Tests\Fixtures\ArrayLogDriver;
use CommonPHP\Logging\Tests\Fixtures\PlainFormatter;
use CommonPHP\Logging\Tests\Fixtures\TemporaryDirectoryTrait;
use PHPUnit\Framework\TestCase;

final class LoggerFactoryTest extends TestCase
{
    use TemporaryDirectoryTrait;

    public function testCreateBuildsManagerWithDefaultOrProvidedDriver(): void
    {
        $factory = new LoggerFactory();
        $default = $factory->create();
        $driver = new ArrayLogDriver();
        $custom = $factory->create($driver);

        self::assertInstanceOf(LogManager::class, $default);
        self::assertInstanceOf(NativeLogDriver::class, $default->getDriver());
        self::assertSame($driver, $custom->getDriver());
    }

    public function testNativeCreatesConfiguredManager(): void
    {
        $received = [];
        $processor = new class implements LogProcessorInterface {
            public function process(LogRecord $record): LogRecord
            {
                return $record->withExtraValue('factory', true);
            }
        };
        $logger = (new LoggerFactory())->native(
            [
                LogTarget::callback(static function (LogRecord $record) use (&$received): void {
                    $received[] = $record;
                }),
            ],
            [$processor],
            'factory',
        );

        $logger->info('Created');

        self::assertSame('factory', $received[0]->channel);
        self::assertSame(['factory' => true], $received[0]->extra);
    }

    public function testFileFactoryWritesToFile(): void
    {
        $directory = $this->createTemporaryDirectory();
        $path = $directory . DIRECTORY_SEPARATOR . 'factory.log';
        $logger = (new LoggerFactory())->file($path, formatter: new PlainFormatter());

        $logger->info('Factory file');

        self::assertSame('Factory file' . PHP_EOL, file_get_contents($path));
    }

    public function testDestinationFactoriesCreateExpectedTargets(): void
    {
        $factory = new LoggerFactory();
        $stderr = $factory->stderr();
        $stdout = $factory->stdout();
        $errorLog = $factory->errorLog();

        self::assertInstanceOf(NativeLogDriver::class, $stderr->getDriver());
        self::assertInstanceOf(NativeLogDriver::class, $stdout->getDriver());
        self::assertInstanceOf(NativeLogDriver::class, $errorLog->getDriver());
        self::assertTrue($stderr->getDriver()->getRouter()->hasTarget('stderr'));
        self::assertTrue($stdout->getDriver()->getRouter()->hasTarget('stdout'));
        self::assertTrue($errorLog->getDriver()->getRouter()->hasTarget('error_log'));
    }
}
