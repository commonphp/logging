<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Contracts;

use CommonPHP\Runtime\Contracts\DriverInterface;
use Psr\Log\LoggerInterface;

interface LogDriverInterface extends DriverInterface, LoggerInterface
{

}
