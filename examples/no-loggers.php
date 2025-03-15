<?php

/** @noinspection PhpUnhandledExceptionInspection */

require '../vendor/autoload.php';
require './include/log-example-container.php';

use Neuron\Logging\LogManager;
use Psr\Log\LogLevel;

$logExampleContainer = new LogExampleContainer();
$log = new LogManager(new LogExampleContainer());
$log->setLevel(LogLevel::DEBUG);
$logExampleContainer->setLog($log);

// Log some messages.
$log->info("Application started");
$log->warning("Low disk space", ['available' => '500MB']);
$log->error("Unable to connect to database", ['db' => 'mysql']);
