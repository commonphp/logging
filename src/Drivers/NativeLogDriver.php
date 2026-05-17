<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Drivers;

use CommonPHP\Logging\Contracts\AbstractLogDriver;
use CommonPHP\Logging\Contracts\LogProcessorInterface;
use CommonPHP\Logging\Contracts\LogTargetInterface;
use CommonPHP\Logging\Exceptions\LoggingException;
use CommonPHP\Logging\Exceptions\LogDriverException;
use CommonPHP\Logging\Exceptions\LogProcessorException;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\LogTarget;
use CommonPHP\Logging\Processors\InterpolationProcessor;
use CommonPHP\Logging\SeverityRouter;
use Stringable;
use Throwable;

final class NativeLogDriver extends AbstractLogDriver
{
    private SeverityRouter $router;

    /**
     * @var list<LogProcessorInterface>
     */
    private array $processors = [];

    /**
     * @param iterable<LogTargetInterface>|null $targets
     * @param iterable<LogProcessorInterface>|null $processors
     */
    public function __construct(
        ?SeverityRouter $router = null,
        ?iterable $targets = null,
        ?iterable $processors = null,
        private readonly string $channel = 'app',
    ) {
        $this->router = $router ?? new SeverityRouter();

        if ($targets === null) {
            $this->router->addTarget(LogTarget::errorLog());
        } else {
            foreach ($targets as $target) {
                $this->addTarget($target);
            }
        }

        if ($processors === null) {
            $this->pushProcessor(new InterpolationProcessor());
        } else {
            foreach ($processors as $processor) {
                $this->pushProcessor($processor);
            }
        }
    }

    public function addTarget(LogTargetInterface $target): self
    {
        $this->router()->addTarget($target);

        return $this;
    }

    public function removeTarget(string $name): self
    {
        $this->router()->removeTarget($name);

        return $this;
    }

    public function pushProcessor(LogProcessorInterface $processor): self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function getRouter(): SeverityRouter
    {
        return $this->router();
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $record = $this->newRecord($message, $level, $context, $this->channel);

        foreach ($this->processors as $processor) {
            try {
                $record = $processor->process($record);
            } catch (LoggingException $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                throw new LogProcessorException(
                    'Log processor ' . $processor::class . ' failed: ' . $exception->getMessage(),
                    $exception->getCode(),
                    $exception,
                );
            }

            if (!$record instanceof LogRecord) {
                throw new LogProcessorException('Log processor ' . $processor::class . ' must return a LogRecord.');
            }
        }

        try {
            $this->router()->dispatch($record);
        } catch (LoggingException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new LogDriverException(
                'Native log driver failed: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }
    }

    private function router(): SeverityRouter
    {
        return $this->router;
    }
}
