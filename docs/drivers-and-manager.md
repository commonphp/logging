# Drivers And Manager

`LogManager` is the PSR-3 logger most application code should use. It delegates actual logging work to a driver.

## LogManager

```php
use CommonPHP\Logging\LogManager;
use CommonPHP\Logging\Drivers\NativeLogDriver;

$logger = new LogManager(new NativeLogDriver());

$logger->info('Application started');
```

The manager can swap drivers:

```php
$logger->useDriver($otherDriver);
```

## Native Driver

`NativeLogDriver` owns:

- a `SeverityRouter`;
- one or more targets;
- zero or more processors;
- a channel name.

```php
use CommonPHP\Logging\Drivers\NativeLogDriver;
use CommonPHP\Logging\LogTarget;

$driver = new NativeLogDriver(
    targets: [LogTarget::file(__DIR__ . '/var/app.log')],
    channel: 'app',
);
```

The native driver is intentionally small. Use an external driver package, such as the Monolog driver, when an application needs rotating files, remote transports, or vendor-specific handlers.

## Driver Contract

Drivers implement `LogDriverInterface`, which extends both the CommonPHP runtime driver contract and `Psr\Log\LoggerInterface`.

`AbstractLogDriver` supplies `getName()` and record creation helpers for custom drivers.
