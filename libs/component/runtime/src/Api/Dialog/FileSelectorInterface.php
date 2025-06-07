<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

interface FileSelectorInterface
{
    /**
     * Opens a system dialog to open a specific file.
     *
     * @param non-empty-string|null $directory
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return non-empty-string|null
     */
    public function selectFile(?string $directory = null, iterable $filter = []): ?string;

    /**
     * Opens a system dialog to open list of specific files.
     *
     * @param non-empty-string|null $directory
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return iterable<array-key, non-empty-string>
     */
    public function selectFiles(?string $directory = null, iterable $filter = []): iterable;
}
