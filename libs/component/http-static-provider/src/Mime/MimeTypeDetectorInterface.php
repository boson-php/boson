<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static\Mime;

interface MimeTypeDetectorInterface
{
    /**
     * @param non-empty-string $pathname
     *
     * @return non-empty-lowercase-string|null
     */
    public function findMimeTypeByFile(string $pathname): ?string;
}
