# CommonPHP Logging Documentation

CommonPHP Logging is the advanced PSR-3 logging package for CommonPHP applications and standalone PHP projects. It provides a simple native logger, log records, level normalization, formatters, processors, targets, severity routing, a factory, and runtime service-provider integration.

Runtime only guarantees that `Psr\Log\LoggerInterface` can be injected. This package supplies the configurable behavior that belongs outside runtime.

## Start Here

- [Getting started](getting-started.md)
- [Usage](usage.md)
- [Package boundaries](package-boundaries.md)

## Logging Concepts

- [Records and levels](records-and-levels.md)
- [Targets and routing](targets-and-routing.md)
- [Formatters and processors](formatters-and-processors.md)
- [Drivers and manager](drivers-and-manager.md)
- [Service provider](service-provider.md)
- [Error handling](error-handling.md)

## Examples

- [Examples index](examples/index.md)
- [File logger](examples/file-logger.md)
- [Runtime service provider](examples/runtime-service-provider.md)
- [Custom target](examples/custom-target.md)

## Development

- [Testing and QA](testing.md)

## Public API Map

Entry points:

- `CommonPHP\Logging\LogManager`
- `CommonPHP\Logging\LoggerFactory`
- `CommonPHP\Logging\LoggingServiceProvider`

Records, levels, and routing:

- `CommonPHP\Logging\LogRecord`
- `CommonPHP\Logging\LogTarget`
- `CommonPHP\Logging\SeverityRouter`
- `CommonPHP\Logging\Enums\LogLevelValue`

Drivers:

- `CommonPHP\Logging\Drivers\NativeLogDriver`

Formatters and processors:

- `CommonPHP\Logging\Formatters\LineFormatter`
- `CommonPHP\Logging\Processors\InterpolationProcessor`

Contracts:

- `CommonPHP\Logging\Contracts\LogDriverInterface`
- `CommonPHP\Logging\Contracts\AbstractLogDriver`
- `CommonPHP\Logging\Contracts\LogFormatterInterface`
- `CommonPHP\Logging\Contracts\LogProcessorInterface`
- `CommonPHP\Logging\Contracts\LogTargetInterface`

Exceptions:

- `CommonPHP\Logging\Exceptions\LoggingException`
- `CommonPHP\Logging\Exceptions\LogDriverException`
- `CommonPHP\Logging\Exceptions\LogFormatterException`
- `CommonPHP\Logging\Exceptions\LogProcessorException`
- `CommonPHP\Logging\Exceptions\LogTargetException`
- `CommonPHP\Logging\Exceptions\InvalidLogTargetException`
- `CommonPHP\Logging\Exceptions\UnwritableLogFileException`
