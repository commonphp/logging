# Formatters And Processors

Processors transform records before they are written. Formatters turn records into strings for targets.

## Interpolation Processor

`InterpolationProcessor` replaces PSR-3 placeholders with safe scalar values from context.

```php
use CommonPHP\Logging\Processors\InterpolationProcessor;

$record = (new InterpolationProcessor())->process($record);
```

The processor interpolates scalar, null, boolean, date-time, and stringable values. Arrays and arbitrary objects remain in context.

## Line Formatter

`LineFormatter` creates compact text lines:

```text
[2026-05-17T10:00:00-05:00] app.info: User created {"context":{"id":42}}
```

Context and extra data are JSON encoded after normalization. Throwables become structured arrays with class, message, code, file, and line.

## Custom Processor

```php
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\LogRecord;

final class RequestIdProcessor implements LogProcessorInterface
{
    public function process(LogRecord $record): LogRecord
    {
        return $record->withExtraValue('request_id', current_request_id());
    }
}
```

## Custom Formatter

```php
use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\LogRecord;

final class MessageOnlyFormatter implements LogFormatterInterface
{
    public function format(LogRecord $record): string
    {
        return $record->message;
    }
}
```
