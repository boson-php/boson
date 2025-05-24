<?php

declare(strict_types=1);

namespace Boson\Bridge\Static\Mime;

interface FileDetectorInterface
{
    /**
     * @param non-empty-string $pathname
     *
     * @return non-empty-lowercase-string|null
     */
    public function detectByFile(string $pathname): ?string;
}
