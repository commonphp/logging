<?php

/** @noinspection PhpUnhandledExceptionInspection */

require '../vendor/autoload.php';
require './include/log-example-container.php';
require './include/example-file-logger.php';

use Neuron\Logging\LogManager;
use Psr\Log\LogLevel;

$logExampleContainer = new LogExampleContainer();
$log = new LogManager(new LogExampleContainer());
$log->setLevel(LogLevel::DEBUG);
$logExampleContainer->setLog($log);

$log->load(ExampleFileLogger::class, [
    'filePath'  => __DIR__ . '/_app.log',  // Specify the log file path.
    'supported' => [LogLevel::INFO, LogLevel::WARNING, LogLevel::ERROR], // Only log these levels.
]);

// Log some messages.
$log->info("Application started");
$log->warning("Low disk space", ['available' => '500MB']);
$log->error("Unable to connect to database", ['db' => 'mysql']);
