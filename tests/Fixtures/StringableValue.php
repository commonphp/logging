<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use Stringable;

final readonly class StringableValue implements Stringable
{
    public function __construct(
        private string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
