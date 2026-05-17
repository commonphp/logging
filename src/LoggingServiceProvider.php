<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use CommonPHP\Runtime\Contracts\ServiceProviderInterface;
use DI\ContainerBuilder;

final class LoggingServiceProvider implements ServiceProviderInterface
{

    public function configure(ContainerBuilder $builder): void
    {
        // TODO: Implement configure() method.
    }
}