# Example: Custom Target

```php
use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\LogRecord;

final class ArrayTarget implements LogTargetInterface
{
    public array $records = [];

    public function getName(): string
    {
        return 'array';
    }

    public function handles(LogRecord $record): bool
    {
        return true;
    }

    public function write(LogRecord $record): void
    {
        $this->records[] = $record->toArray();
    }
}
```

Use the custom target with the native driver:

```php
use CommonPHP\Logging\LoggerFactory;

$target = new ArrayTarget();
$logger = (new LoggerFactory())->native([$target]);

$logger->info('Stored in memory');
```
