# Getting Started

CommonPHP Logging can be used directly or through the runtime container.

## Direct Logger

```php
use CommonPHP\Logging\LoggerFactory;

$logger = (new LoggerFactory())->file(__DIR__ . '/var/app.log');

$logger->info('User {id} signed in', [
    'id' => 42,
]);
```

The factory returns a `LogManager`, which implements `Psr\Log\LoggerInterface`.

## Runtime Integration

Register `LoggingServiceProvider` with a runtime kernel when application services should receive this package through `LoggerInterface`.

```php
use CommonPHP\Logging\LoggingServiceProvider;

$kernel->useServiceProvider(new LoggingServiceProvider());
```

Services can then depend on `Psr\Log\LoggerInterface`.

## Default Native Behavior

`NativeLogDriver` writes to PHP's `error_log` by default and uses `InterpolationProcessor` for PSR-3 message placeholders.

For explicit destinations, prefer constructing the logger with named targets:

```php
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\LoggerFactory;

$logger = (new LoggerFactory())->native([
    LogTarget::file(__DIR__ . '/var/app.log'),
]);
```
