<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

interface FileSelectorInterface
{
    /**
     * Opens a system dialog to open a specific file.
     *
     * @param non-empty-string|null $path
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return non-empty-string|null
     */
    public function selectFile(?string $path = null, iterable $filter = []): ?string;

    /**
     * Opens a system dialog to open a specific directory.
     *
     * @param non-empty-string|null $path
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return non-empty-string|null
     */
    public function selectDirectory(?string $path = null, iterable $filter = []): ?string;

    /**
     * Opens a system dialog to open a list of specific files.
     *
     * @param non-empty-string|null $path
     * @param iterable<mixed, non-empty-string> $filter
     *
     * @return iterable<array-key, non-empty-string>
     */
    public function selectFiles(?string $path = null, iterable $filter = []): iterable;
}
