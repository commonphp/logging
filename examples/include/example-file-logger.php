<?php /** @noinspection PhpUnusedParameterInspection */

// A simple console logger implementation
use Neuron\Logging\LogHandlerInterface;
use Psr\Log\LogLevel;

class ExampleFileLogger implements LogHandlerInterface
{
    private ?string $filename = null;
    private array $supported = [];
    public function setup(array $options): void {
        $this->filename = $options['filePath'];
        $this->supported = $options['supported'];
    }

    public function supports(string $level): bool {
        return in_array($level, $this->supported);
    }

    public function log($level, $message, array $context = []): void {
        // Print the log to the console.
        $output = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            !empty($context) ? json_encode($context) : ''
        );

        file_put_contents($this->filename, $output, FILE_APPEND | LOCK_EX);
    }

    // Implement PSR-3 methods by delegating to log()
    public function emergency($message, array $context = []): void { $this->log(LogLevel::EMERGENCY, $message, $context); }
    public function alert($message, array $context = []): void     { $this->log(LogLevel::ALERT, $message, $context); }
    public function critical($message, array $context = []): void  { $this->log(LogLevel::CRITICAL, $message, $context); }
    public function error($message, array $context = []): void     { $this->log(LogLevel::ERROR, $message, $context); }
    public function warning($message, array $context = []): void   { $this->log(LogLevel::WARNING, $message, $context); }
    public function notice($message, array $context = []): void    { $this->log(LogLevel::NOTICE, $message, $context); }
    public function info($message, array $context = []): void      { $this->log(LogLevel::INFO, $message, $context); }
    public function debug($message, array $context = []): void     { $this->log(LogLevel::DEBUG, $message, $context); }
}