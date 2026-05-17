<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Unit;

use CommonPHP\Logging\Exceptions\InvalidLogTargetException;
use CommonPHP\Logging\Exceptions\LogTargetException;
use CommonPHP\Logging\LogRecord;
use CommonPHP\Logging\SeverityRouter;
use CommonPHP\Logging\Tests\Fixtures\CapturingTarget;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SeverityRouterTest extends TestCase
{
    public function testItRegistersLooksUpAndRemovesTargets(): void
    {
        $target = new CapturingTarget('main');
        $router = new SeverityRouter([$target]);

        self::assertFalse($router->isEmpty());
        self::assertTrue($router->hasTarget('main'));
        self::assertSame($target, $router->getTarget('main'));
        self::assertSame(['main' => $target], $router->getTargets());

        $router->removeTarget('main');

        self::assertTrue($router->isEmpty());
        self::assertFalse($router->hasTarget('main'));
    }

    public function testItRejectsEmptyAndDuplicateTargetNames(): void
    {
        $router = new SeverityRouter();

        $this->expectException(InvalidLogTargetException::class);
        $router->addTarget(new CapturingTarget(''));
    }

    public function testItRejectsDuplicateTargetNames(): void
    {
        $router = new SeverityRouter([new CapturingTarget('same')]);

        $this->expectException(InvalidLogTargetException::class);
        $this->expectExceptionMessage('already registered');

        $router->addTarget(new CapturingTarget('same'));
    }

    public function testMissingTargetsAreRejected(): void
    {
        $this->expectException(InvalidLogTargetException::class);
        $this->expectExceptionMessage('is not registered');

        (new SeverityRouter())->getTarget('missing');
    }

    public function testDispatchWritesOnlyHandledTargets(): void
    {
        $handled = new CapturingTarget('handled', true);
        $ignored = new CapturingTarget('ignored', false);
        $record = new LogRecord('info', 'Dispatch');

        (new SeverityRouter([$handled, $ignored]))->dispatch($record);

        self::assertSame([$record], $handled->records);
        self::assertSame([], $ignored->records);
    }

    public function testDispatchWithoutTargetsIsANoop(): void
    {
        $router = new SeverityRouter();
        $router->dispatch(new LogRecord('info', 'No targets'));

        self::assertTrue($router->isEmpty());
    }

    public function testDispatchWrapsUnexpectedTargetFailures(): void
    {
        $router = new SeverityRouter([
            new CapturingTarget('bad', true, new RuntimeException('Write failed')),
        ]);

        try {
            $router->dispatch(new LogRecord('info', 'Failure'));
            self::fail('Expected target failure.');
        } catch (LogTargetException $exception) {
            self::assertStringContainsString('Log target "bad" failed', $exception->getMessage());
            self::assertInstanceOf(RuntimeException::class, $exception->getPrevious());
        }
    }

    public function testDispatchPassesLoggingTargetFailuresThrough(): void
    {
        $expected = new LogTargetException('Known target failure.');
        $router = new SeverityRouter([
            new CapturingTarget('bad', true, $expected),
        ]);

        try {
            $router->dispatch(new LogRecord('info', 'Failure'));
            self::fail('Expected target failure.');
        } catch (LogTargetException $exception) {
            self::assertSame($expected, $exception);
        }
    }
}
