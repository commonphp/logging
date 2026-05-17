# Records And Levels

`LogRecord` is the internal immutable value object passed from drivers through processors, formatters, and targets.

## Record Fields

A record contains:

- timestamp;
- normalized level;
- message;
- context;
- channel;
- extra values.

```php
use CommonPHP\Logging\LogRecord;

$record = new LogRecord(
    level: 'info',
    message: 'Account created',
    context: ['account_id' => 42],
    channel: 'billing',
);
```

## Immutability

Use `with*` methods to derive a changed record.

```php
$processed = $record
    ->withMessage('Processed account')
    ->withExtraValue('request_id', 'abc123');
```

The original record remains unchanged.

## Levels

`LogLevelValue` supports the PSR-3 levels:

- `debug`
- `info`
- `notice`
- `warning`
- `error`
- `critical`
- `alert`
- `emergency`

Levels can be created from strings, stringable values, or existing enum cases.

```php
use CommonPHP\Logging\Enums\LogLevelValue;

$level = LogLevelValue::fromLevel('warning');

if ($level->isAtLeast(LogLevelValue::Error)) {
    // route to high-severity target
}
```
