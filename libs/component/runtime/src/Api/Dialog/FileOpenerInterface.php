<?php

declare(strict_types=1);

namespace Boson\Api\Dialog;

interface FileOpenerInterface
{
    /**
     * Opens a file using a registered application in the system.
     *
     * For example, when pass:
     *
     * - A `/path/to/text.txt` file, the system editor opens.
     * - A `http://example.com` file, the system browser opens.
     *
     * @param non-empty-string $url
     */
    public function open(string $url): void;
}
