# Service Provider

`LoggingServiceProvider` registers the package with the CommonPHP runtime container.

## Registered Definitions

The provider registers:

- `LogFormatterInterface` as `LineFormatter`;
- `LogProcessorInterface` as `InterpolationProcessor`;
- `LoggerFactory`;
- `NativeLogDriver`;
- `LogDriverInterface` as the native driver;
- `LogManager`;
- `Psr\Log\LoggerInterface` as the log manager.

## Runtime Usage

```php
use CommonPHP\Logging\LoggingServiceProvider;

$kernel->useServiceProvider(new LoggingServiceProvider());
```

Services can then request `Psr\Log\LoggerInterface`:

```php
use Psr\Log\LoggerInterface;

final class ImportService
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function run(): void
    {
        $this->logger->info('Import started');
    }
}
```

## Customizing

Applications that need a custom driver can bind `LogDriverInterface` or `LoggerInterface` after this provider, using runtime container configurators.
