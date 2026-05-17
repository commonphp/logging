# Package Boundaries

CommonPHP Logging owns advanced logging behavior for CommonPHP applications.

Runtime owns only the minimal `Psr\Log\LoggerInterface` binding point and its `NullLogger` fallback.

## Belongs In Logging

- PSR-3 logger implementations.
- Log records and level normalization.
- Targets and severity routing.
- Formatters and processors.
- File, stderr, stdout, PHP error log, and callback destinations.
- Runtime service-provider registration for the logging package.
- Logging-specific exceptions.
- Driver contracts for logging integrations.

## Does Not Belong In Logging

- Runtime kernel execution.
- HTTP request and response handling.
- Routing.
- Database persistence.
- Queue workers.
- User interface rendering.
- Application-specific audit policy.
- Vendor-specific logging integrations that need large dependencies.

## Driver Packages

Vendor integrations should live in driver packages when they require extra dependencies or vendor-specific configuration.

For example, a Monolog integration can implement `LogDriverInterface` without forcing the core logging package to depend on Monolog.
