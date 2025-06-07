<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

interface DirectorySelectorInterface
{
    /**
     * Opens a system dialog to open a specific directory.
     *
     * @param non-empty-string|null $directory
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return non-empty-string|null
     */
    public function selectDirectory(?string $directory = null, iterable $filter = []): ?string;

    /**
     * Opens a system dialog to open a list of specific directories.
     *
     * @param non-empty-string|null $directory
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return iterable<array-key, non-empty-string>
     */
    public function selectDirectories(?string $directory = null, iterable $filter = []): iterable;
}
