<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Tests\Fixtures\ArrayLogDriver;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;

final class AbstractLogDriverTest extends TestCase
{
    public function testItProvidesDefaultDriverName(): void
    {
        $driver = new ArrayLogDriver();

        self::assertSame(ArrayLogDriver::class, $driver->getName());
    }

    public function testItCreatesNormalizedRecordsForDrivers(): void
    {
        $driver = new ArrayLogDriver('testing');

        $driver->warning('Careful {name}', ['name' => 'Ada']);

        self::assertCount(1, $driver->records);
        self::assertSame('warning', $driver->records[0]->level->value);
        self::assertSame('Careful {name}', $driver->records[0]->message);
        self::assertSame(['name' => 'Ada'], $driver->records[0]->context);
        self::assertSame('testing', $driver->records[0]->channel);
    }

    public function testItRaisesPsrExceptionForInvalidLevelType(): void
    {
        $driver = new ArrayLogDriver();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported log level type "array".');

        $driver->log([], 'Invalid');
    }
}
