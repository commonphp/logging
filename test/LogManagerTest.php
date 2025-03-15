<?php

declare(strict_types=1);

namespace NeuronTests\Logging;

use NeuronTests\Logging\Fixtures\DummyContainer;
use NeuronTests\Logging\Fixtures\DummyLogHandler;
use NeuronTests\Logging\Fixtures\SpecificLogger;
use PHPUnit\Framework\TestCase;
use Neuron\Logging\LogManager;
use Neuron\Logging\Exceptions\HandlerMissingInterfaceException;
use Psr\Log\LogLevel;
use stdClass;

/**
 * Unit tests for the logging library.
 */
final class LogManagerTest extends TestCase
{
    public function testAddLoggerAndDispatchMessage(): void
    {
        $manager = new LogManager(new DummyContainer());
        $manager->setLevel(LogLevel::DEBUG);
        $dummy = $manager->load(DummyLogHandler::class);

        // Dispatch an info-level message.
        $manager->info("Test info", ['key' => 'value']);

        $this->assertNotEmpty($dummy->messages, "Expected at least one log message.");
        $logged = $dummy->messages[0];
        $this->assertEquals(LogLevel::INFO, $logged['level']);
    }

    public function testLoggerSupportsFiltering(): void
    {
        $manager = new LogManager(new DummyContainer());
        $manager->setLevel(LogLevel::DEBUG);

        $infoLogger = $manager->load(SpecificLogger::class, ['level' => LogLevel::INFO]);
        $errorLogger = $manager->load(SpecificLogger::class, ['level' => LogLevel::ERROR]);

        // Log messages at different levels.
        $manager->info("Info message");
        $manager->error("Error message");

        // Each logger should only log the message it supports.
        $this->assertCount(1, $infoLogger->messages, "Info logger should have received 1 message.");
        $this->assertEquals(LogLevel::INFO, $infoLogger->messages[0]['level']);

        $this->assertCount(1, $errorLogger->messages, "Error logger should have received 1 message.");
        $this->assertEquals(LogLevel::ERROR, $errorLogger->messages[0]['level']);
    }

    public function testMultipleLoggersReceiveMessages(): void
    {
        $manager = new LogManager(new DummyContainer());
        $logger1 = $manager->load(DummyLogHandler::class);
        $logger2 = $manager->load(DummyLogHandler::class);

        $manager->warning("Warning message");

        $this->assertCount(1, $logger1->messages, "Logger1 should have received the warning message.");
        $this->assertCount(1, $logger2->messages, "Logger2 should have received the warning message.");
    }

    public function testInvalidLoggerThrowsException(): void
    {
        $this->expectException(HandlerMissingInterfaceException::class);
        $manager = new LogManager(new DummyContainer());

        // Attempting to add an object that doesn't implement LoggerInterface should throw an exception.
        $manager->load(StdClass::class);
    }

    public function testLogLevelThresholdOption(): void
    {
        $manager = new LogManager(new DummyContainer());
        $dummy = $manager->load(DummyLogHandler::class);

        $manager->log(LogLevel::INFO, "Info message");
        $manager->log(LogLevel::WARNING, "Warning message");

        // Only the warning should be logged.
        $this->assertCount(1, $dummy->messages, "Only one message should be logged due to threshold filtering.");
        $this->assertEquals(LogLevel::WARNING, $dummy->messages[0]['level']);
    }
}
