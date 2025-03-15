# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2025-03-14

### Added
- Initial release of the lean logging library.
  - PSR-3 compliant `LoggerInterface` with additional `setup` and `supports` methods.
  - `LogManager` for handling multiple logger instances.
  - Built-in loggers including `BuiltinLogHandler` and `NullLogHandler`.
  - Support for log level filtering and custom logger configuration.
