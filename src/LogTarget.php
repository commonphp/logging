<?php

declare(strict_types=1);

namespace CommonPHP\Logging;

use Closure;
use CommonPHP\Logging\Contracts\LogFormatterInterface;
use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\Enums\LogLevelValue;
use CommonPHP\Logging\Exceptions\InvalidLogTargetException;
use CommonPHP\Logging\Exceptions\LogFormatterException;
use CommonPHP\Logging\Exceptions\LogTargetException;
use CommonPHP\Logging\Exceptions\UnwritableLogFileException;
use CommonPHP\Logging\Formatters\LineFormatter;
use InvalidArgumentException;
use Stringable;
use Throwable;

final class LogTarget implements LogTargetInterface
{
    private const MODE_STREAM = 'stream';
    private const MODE_FILE = 'file';
    private const MODE_ERROR_LOG = 'error_log';
    private const MODE_CALLBACK = 'callback';

    private string $mode = self::MODE_STREAM;

    private ?Closure $callback = null;

    private LogFormatterInterface $formatter;

    private LogLevelValue $minimumLevel;

    /**
     * @var array<string, true>
     */
    private array $levels = [];

    /**
     * @param array<LogLevelValue|Stringable|string> $levels
     */
    public function __construct(
        private readonly string $name = 'stderr',
        private ?string $destination = 'php://stderr',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
        private readonly bool $appendNewLine = true,
    ) {
        if ($this->name === '') {
            throw new InvalidLogTargetException('Log target names cannot be empty.');
        }

        $this->formatter = $formatter ?? new LineFormatter();
        $this->minimumLevel = $this->normalizeLevel($minimumLevel);
        $this->levels = $this->normalizeLevels($levels);
    }

    public static function stdout(
        string $name = 'stdout',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
    ): self {
        return new self($name, 'php://stdout', $formatter, $minimumLevel, $levels);
    }

    public static function stderr(
        string $name = 'stderr',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
    ): self {
        return new self($name, 'php://stderr', $formatter, $minimumLevel, $levels);
    }

    public static function file(
        string $path,
        string $name = 'file',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
    ): self {
        $target = new self($name, $path, $formatter, $minimumLevel, $levels);
        $target->mode = self::MODE_FILE;

        return $target;
    }

    public static function errorLog(
        string $name = 'error_log',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
    ): self {
        $target = new self($name, null, $formatter, $minimumLevel, $levels);
        $target->mode = self::MODE_ERROR_LOG;

        return $target;
    }

    public static function callback(
        callable $callback,
        string $name = 'callback',
        ?LogFormatterInterface $formatter = null,
        LogLevelValue|Stringable|string $minimumLevel = LogLevelValue::Debug,
        array $levels = [],
    ): self {
        $target = new self($name, null, $formatter, $minimumLevel, $levels);
        $target->mode = self::MODE_CALLBACK;
        $target->callback = Closure::fromCallable($callback);

        return $target;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function handles(LogRecord $record): bool
    {
        if ($this->levels !== []) {
            return isset($this->levels[$record->level->value]);
        }

        return $record->level->isAtLeast($this->minimumLevel);
    }

    public function write(LogRecord $record): void
    {
        if (!$this->handles($record)) {
            return;
        }

        try {
            $line = $this->terminateLine($this->formatter->format($record));
        } catch (LogFormatterException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new LogFormatterException(
                'Failed to format log record for target "' . $this->name . '": ' . $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }

        match ($this->mode) {
            self::MODE_FILE => $this->writeFile($line),
            self::MODE_ERROR_LOG => $this->writeErrorLog($line),
            self::MODE_CALLBACK => $this->writeCallback($record, $line),
            default => $this->writeStream($line),
        };
    }

    private function terminateLine(string $line): string
    {
        if (!$this->appendNewLine || str_ends_with($line, "\n")) {
            return $line;
        }

        return $line . PHP_EOL;
    }

    private function writeStream(string $line): void
    {
        if ($this->destination === null || $this->destination === '') {
            throw new InvalidLogTargetException('Stream log target "' . $this->name . '" has no destination.');
        }

        if (@file_put_contents($this->destination, $line) === false) {
            throw new LogTargetException(
                'Unable to write to stream target "' . $this->name . '" at "' . $this->destination . '".',
            );
        }
    }

    private function writeFile(string $line): void
    {
        $path = $this->destination;

        if ($path === null || $path === '') {
            throw new InvalidLogTargetException('File log target "' . $this->name . '" has no path.');
        }

        $this->ensureWritableFile($path);

        if (@file_put_contents($path, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new UnwritableLogFileException('Unable to append to log file "' . $path . '".');
        }
    }

    private function writeErrorLog(string $line): void
    {
        if (!error_log(rtrim($line, "\r\n"))) {
            throw new LogTargetException('Unable to write to PHP error log.');
        }
    }

    private function writeCallback(LogRecord $record, string $line): void
    {
        if ($this->callback === null) {
            throw new InvalidLogTargetException('Callback log target "' . $this->name . '" has no callback.');
        }

        ($this->callback)($record, $line);
    }

    private function ensureWritableFile(string $path): void
    {
        if (is_dir($path)) {
            throw new UnwritableLogFileException('Log path "' . $path . '" is a directory.');
        }

        $directory = dirname($path);

        if ($directory !== '' && $directory !== '.' && !is_dir($directory)) {
            if (!@mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new UnwritableLogFileException('Unable to create log directory "' . $directory . '".');
            }
        }

        if (file_exists($path) && !is_writable($path)) {
            throw new UnwritableLogFileException('Log file "' . $path . '" is not writable.');
        }

        if (!file_exists($path) && $directory !== '' && $directory !== '.' && !is_writable($directory)) {
            throw new UnwritableLogFileException('Log directory "' . $directory . '" is not writable.');
        }
    }

    private function normalizeLevel(LogLevelValue|Stringable|string $level): LogLevelValue
    {
        try {
            return LogLevelValue::fromLevel($level);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidLogTargetException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param array<LogLevelValue|Stringable|string> $levels
     * @return array<string, true>
     */
    private function normalizeLevels(array $levels): array
    {
        $normalized = [];

        foreach ($levels as $level) {
            $normalized[$this->normalizeLevel($level)->value] = true;
        }

        return $normalized;
    }
}
