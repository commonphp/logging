# Targets And Routing

Targets are destinations. The router owns a set of named targets and dispatches each record to the targets that handle it.

## Built-In Target Factories

```php
use CommonPHP\Logging\LogTarget;

$file = LogTarget::file(__DIR__ . '/var/app.log');
$stdout = LogTarget::stdout();
$stderr = LogTarget::stderr();
$errorLog = LogTarget::errorLog();
$callback = LogTarget::callback(static function ($record, string $line): void {
    // custom delivery
});
```

## Minimum Severity

By default, a target handles `debug` and higher.

```php
use CommonPHP\Logging\Enums\LogLevelValue;
use CommonPHP\Logging\LogTarget;

$errors = LogTarget::stderr(minimumLevel: LogLevelValue::Error);
```

## Exact Levels

Pass exact levels when a target should handle only specific levels.

```php
$audit = LogTarget::file(
    path: __DIR__ . '/var/audit.log',
    levels: ['notice', 'warning'],
);
```

Exact levels take precedence over minimum severity.

## Router

```php
use CommonPHP\Logging\SeverityRouter;

$router = new SeverityRouter([$file, $errors]);
$router->dispatch($record);
```

Target names must be unique and non-empty. Routing failures are reported as logging target exceptions.
