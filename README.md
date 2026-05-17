# CommonPHP Logging

CommonPHP Logging provides configurable PSR-3 logging setup for CommonPHP applications. It defines logging drivers, targets, formatting, severity routing, and integrations with established logging libraries.

The package builds on runtime’s basic logger support and provides the more advanced logging behavior that does not belong in the runtime package itself.

## Requirements

- PHP `^8.5`
- `comphp/runtime:^0.3`
- `psr/log:^3.0`

## Installation

Once this package is available through your Composer repositories, install it with:

```bash
composer require comphp/logging
```

## Usage

```php
<?php

// TODO: Write usage
```

## Package Notes

This package should provide configurable logging setup, logger drivers, targets, formatters, processors, severity routing, and runtime integration. Runtime itself only exposes PSR-3 logging and a `NullLogger` fallback.

## Error Handling

Invalid logging targets, formatter failures, unwritable log files, and driver failures should throw CommonPHP logging exceptions.

## Documentation

- [Usage](docs/usage.md)
- [Testing](TESTING.md)
- [Contributing](CONTRIBUTING.md)
- [Security](SECURITY.md)

## License

MIT. See [LICENSE.md](LICENSE.md).
