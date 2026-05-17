# Usage

The package has three common usage styles: factory-created loggers, manually assembled native drivers, and runtime service-provider registration.

## Factory-Created Loggers

```php
use CommonPHP\Logging\LoggerFactory;

$factory = new LoggerFactory();

$fileLogger = $factory->file(__DIR__ . '/var/app.log');
$stderrLogger = $factory->stderr();
$errorLogLogger = $factory->errorLog();
```

Each method returns a `LogManager`, so all PSR-3 methods are available:

```php
$fileLogger->warning('Disk usage is {percent}%', [
    'percent' => 91,
]);
```

## Native Driver With Multiple Targets

```php
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\LoggerFactory;
use CommonPHP\Logging\Enums\LogLevelValue;

$logger = (new LoggerFactory())->native([
    LogTarget::file(__DIR__ . '/var/app.log'),
    LogTarget::stderr(minimumLevel: LogLevelValue::Error),
]);
```

Every target decides whether it handles a record. In this example, all levels go to the file and errors or higher also go to stderr.

## Callback Targets

Callback targets are useful in tests or when adapting records to another system.

```php
use CommonPHP\Logging\LogTarget;

$logger = (new LoggerFactory())->native([
    LogTarget::callback(static function ($record, string $line): void {
        send_to_collector($record->toArray(), $line);
    }),
]);
```

## Message Placeholders

The default native driver uses PSR-3 style interpolation:

```php
$logger->info('Queued job {job}', [
    'job' => 'invoice:send',
]);
```

Scalar, null, boolean, date-time, and stringable context values can be interpolated. Arrays and arbitrary objects stay in context for the formatter.
