<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Logging\Contracts\LogDriverInterface;
use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\Formatters\LineFormatter;
use CommonPHP\Logging\Processors\InterpolationProcessor;
use CommonPHP\Runtime\Contracts\ServiceProviderInterface;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

use function DI\autowire;
use function DI\factory;
use function DI\get;

final class LoggingServiceProvider implements ServiceProviderInterface
{

    public function configure(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            LogFormatterInterface::class => autowire(LineFormatter::class),
            LogProcessorInterface::class => autowire(InterpolationProcessor::class),
            LoggerFactory::class => autowire(LoggerFactory::class),
            NativeLogDriver::class => factory(static fn (): NativeLogDriver => new NativeLogDriver()),
            LogDriverInterface::class => get(NativeLogDriver::class),
            LogManager::class => factory(
                static fn (LogDriverInterface $driver): LogManager => new LogManager($driver),
            ),
            LoggerInterface::class => get(LogManager::class),
        ]);
    }
}
