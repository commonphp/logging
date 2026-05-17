# Testing And QA

CommonPHP Logging includes a package-local PHPUnit configuration and unit tests.

## Install Dependencies

From the package directory:

```bash
composer install
```

From the monorepo, the root `vendor` directory can also satisfy the test suite because `tests/bootstrap.php` checks both package and workspace autoloaders.

## Run PHPUnit

From the monorepo root:

```bash
vendor/bin/phpunit -c package/logging/phpunit.xml.dist
```

From `package/logging`:

```bash
../../vendor/bin/phpunit -c phpunit.xml.dist
```

## Current Test Coverage

The unit suite covers:

- `LogLevelValue` normalization, ordering, and value lists;
- `LogRecord` defaults, immutability helpers, channel and extra changes, and array serialization;
- `InterpolationProcessor` placeholder replacement and unsupported context values;
- `LineFormatter` line shape, context and extra encoding, value normalization, disabled sections, max-depth handling, and JSON failures;
- `LogTarget` target factories, severity checks, exact levels, callback delivery, stream writes, file writes, directory creation, error-log writes, newline handling, invalid targets, unwritable paths, and formatter failure behavior;
- `SeverityRouter` target registration, duplicate detection, lookup, removal, no-op dispatch, handled-only dispatch, and exception wrapping;
- `NativeLogDriver` default setup, interpolation, channel forwarding, target and processor registration, invalid levels, processor failures, and target failures;
- `LogManager` driver delegation, driver swapping, native extension helpers, and unsupported driver behavior;
- `LoggerFactory` factory methods and concrete destination helpers;
- `LoggingServiceProvider` PHP-DI bindings and PSR logger resolution;
- exception hierarchy and abstract driver helpers.

## Manual Review Areas

Manual review should still cover production log retention, deployment-level permissions, external collector behavior, and vendor driver packages.
