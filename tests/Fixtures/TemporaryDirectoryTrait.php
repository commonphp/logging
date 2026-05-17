<?php

declare(strict_types=1);

namespace CommonPHP\Logging\Tests\Fixtures;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

trait TemporaryDirectoryTrait
{
    /**
     * @var list<string>
     */
    private array $temporaryDirectories = [];

    protected function createTemporaryDirectory(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'commonphp-logging-' . bin2hex(random_bytes(8));

        if (!mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to create temporary directory: ' . $path);
        }

        $this->temporaryDirectories[] = $path;

        return $path;
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->temporaryDirectories) as $directory) {
            $this->removeTemporaryDirectory($directory);
        }

        $this->temporaryDirectories = [];

        parent::tearDown();
    }

    private function removeTemporaryDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
                continue;
            }

            unlink($item->getPathname());
        }

        rmdir($directory);
    }
}
