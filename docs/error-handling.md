# Error Handling

The package follows PSR-3 for invalid log levels and uses CommonPHP logging exceptions for package failures.

## Invalid Levels

Passing an unsupported level through a PSR logger raises `Psr\Log\InvalidArgumentException`.

```php
$logger->log('verbose', 'Unsupported');
```

## Logging Exceptions

All package-specific exceptions extend `CommonPHP\Logging\Exceptions\LoggingException`.

Common cases:

- invalid target names or duplicate target registration;
- unwritable file targets;
- formatter failures;
- processor failures;
- driver failures.

## Target Failures

`SeverityRouter` wraps unexpected target failures in `LogTargetException`. Existing `LogTargetException` instances are passed through unchanged.

## Formatter And Processor Failures

Formatter failures are reported as `LogFormatterException`.

Processor failures are reported as `LogProcessorException` unless the processor already throws a logging exception.

## Context Values

`LineFormatter` normalizes common non-scalar context values. It serializes throwables, date-time objects, stringable values, objects, resources, and deep arrays into safe representations before JSON encoding.
