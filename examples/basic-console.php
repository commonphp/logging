<?php

/** @noinspection PhpUnhandledExceptionInspection */

require '../vendor/autoload.php';
require './include/log-example-container.php';
require './include/example-console-logger.php';

use Neuron\Logging\LogManager;
use Psr\Log\LogLevel;

$logExampleContainer = new LogExampleContainer();
$log = new LogManager(new LogExampleContainer());
$log->setLevel(LogLevel::DEBUG);
$logExampleContainer->setLog($log);

$log->load(ConsoleLogger::class);

// Dispatch some log messages.
$log->info("Starting the application", ['user' => 'JaneDoe']);
$log->error("A critical error occurred", ['error_code' => 500]);
