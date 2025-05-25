<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static\Mime;

use finfo as ExtFileInfo;

final readonly class ExtInfoMimeTypeDetector implements MimeTypeDetectorInterface
{
    private ?ExtFileInfo $finfo;

    public function __construct(
        private ?MimeTypeDetectorInterface $delegate = null,
        /**
         * Provides reference to "magic" `ext-finfo` file.
         *
         * @var non-empty-string|null
         */
        private ?string $magic = null,
    ) {
        $this->finfo = $this->tryCreateFileInfo();
    }

    private function tryCreateFileInfo(): ?ExtFileInfo
    {
        if (!\extension_loaded('fileinfo')) {
            return null;
        }

        return $this->createFileInfo();
    }

    private function createFileInfo(): ExtFileInfo
    {
        if ($this->magic === null || $this->magic === '') {
            return new ExtFileInfo(\FILEINFO_MIME_TYPE);
        }

        return new ExtFileInfo(\FILEINFO_MIME_TYPE, $this->magic);
    }

    public function findMimeTypeByFile(string $pathname): ?string
    {
        $result = $this->finfo?->file($pathname);

        if (\is_string($result) && $result !== '') {
            return \strtolower($result);
        }

        return $this->delegate?->findMimeTypeByFile($pathname);
    }
}
