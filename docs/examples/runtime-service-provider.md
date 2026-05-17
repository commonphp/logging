# Example: Runtime Service Provider

```php
use CommonPHP\Logging\LoggingServiceProvider;

$kernel
    ->useServiceProvider(new LoggingServiceProvider())
    ->setExecutive(AppExecutive::class);
```

Application services can then depend on `Psr\Log\LoggerInterface`.

```php
use Psr\Log\LoggerInterface;

final class AppExecutive
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(): int
    {
        $this->logger->notice('Executive started');

        return 0;
    }
}
```
