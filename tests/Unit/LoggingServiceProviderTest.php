<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Contracts\LogDriverInterface;
use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Formatters\LineFormatter;
use CommonPHP\Logging\LoggerFactory;
use CommonPHP\Logging\LoggingServiceProvider;
use CommonPHP\Logging\LogManager;
use CommonPHP\Logging\Processors\InterpolationProcessor;
use CommonPHP\Runtime\Contracts\ServiceProviderInterface;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LoggingServiceProviderTest extends TestCase
{
    public function testItImplementsRuntimeServiceProvider(): void
    {
        self::assertInstanceOf(ServiceProviderInterface::class, new LoggingServiceProvider());
    }

    public function testItRegistersLoggingDefinitions(): void
    {
        $builder = new ContainerBuilder();
        (new LoggingServiceProvider())->configure($builder);
        $container = $builder->build();

        $nativeDriver = $container->get(NativeLogDriver::class);
        $driver = $container->get(LogDriverInterface::class);
        $manager = $container->get(LogManager::class);
        $psrLogger = $container->get(LoggerInterface::class);

        self::assertInstanceOf(LineFormatter::class, $container->get(LogFormatterInterface::class));
        self::assertInstanceOf(InterpolationProcessor::class, $container->get(LogProcessorInterface::class));
        self::assertInstanceOf(LoggerFactory::class, $container->get(LoggerFactory::class));
        self::assertSame($nativeDriver, $driver);
        self::assertSame($driver, $manager->getDriver());
        self::assertSame($manager, $psrLogger);
    }
}
