# Example: File Logger

```php
use CommonPHP\Logging\LoggerFactory;

$logger = (new LoggerFactory())->file(__DIR__ . '/../var/app.log');

$logger->info('Application started');
$logger->warning('Payment retry scheduled for invoice {invoice}', [
    'invoice' => 'INV-1001',
]);
```

The file target creates missing parent directories when possible and appends formatted log lines with a lock.
