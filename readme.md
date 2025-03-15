# Lean Logging Library

A minimal, PSR-3 compliant logging library designed for flexibility and simplicity. It supports multiple log outputs, custom logger configurations, and log level filtering—keeping things as lean as your favorite retro toolkit.

## Features

- **PSR-3 Compliant:** Implements the standard logging interface.
- **Custom Logger Implementations:** Includes examples like `BuiltinLogger` and `NullLogger`.
- **LogManager:** Easily handle and dispatch log messages to one or more logger instances.
- **Flexible Configuration:** Configure logger behavior, including log level filtering and custom setups.
- **Multiple Outputs:** Log to multiple destinations without breaking standards.

## Installation

Install via Composer:

```bash
composer require comphp/logging
```

## Usage

Create and configure logger instances, then add them to the LogManager:

```php
<?php
require 'vendor/autoload.php';

use Neuron\Logging\LogManager;
use Neuron\Logging\LogLevel;
use Neuron\Logging\Loggers\FileLogger;

// Initialize the log manager
$logManager = new LogManager($container);

$logManager->load(ThirdPartyFileLogger::class, [
    'filePath'  => __DIR__ . '/logs/app.log',
    'supported' => [LogLevel::INFO, LogLevel::WARNING, LogLevel::ERROR],
])

// Dispatch some log messages
$logManager->info("Application started", ['user' => 'Alice']);
$logManager->error("Unhandled exception", ['exception' => 'RuntimeException']);
```

## Examples

Additional example scripts are provided in the `examples/` directory, demonstrating how to use different logger implementations and configurations.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a record of changes.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to help out.

## Code of Conduct

This project is released under a Code of Conduct. Please review [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md) for details.

## License

This project is licensed under the [MIT License](LICENSE.md). See the LICENSE file for more information.
