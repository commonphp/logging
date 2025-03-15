<?php

/** @noinspection PhpUnhandledExceptionInspection */

require '../vendor/autoload.php';
require './include/log-example-container.php';
require './include/example-console-logger.php';
require './include/example-file-logger.php';

use Neuron\Logging\LogManager;
use Psr\Log\LogLevel;

$logExampleContainer = new LogExampleContainer();
$log = new LogManager(new LogExampleContainer());
$log->setLevel(LogLevel::DEBUG);
$logExampleContainer->setLog($log);

$log->load(ConsoleLogger::class);

$log->load(ExampleFileLogger::class, [
    'filePath'  => __DIR__ . '/_combined.log',  // Specify the log file path.
    'supported' => [LogLevel::INFO, LogLevel::WARNING, LogLevel::WARNING], // Only log these levels.
]);

// Log some messages.
$log->debug("Debugging application");
$log->info("User login", ['user' => 'Alice']);
$log->error("Unhandled exception", ['exception' => 'RuntimeException']);
