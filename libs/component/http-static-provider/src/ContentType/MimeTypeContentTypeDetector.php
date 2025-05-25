<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static\ContentType;

use Boson\Component\Http\Static\Mime\ExtensionMimeTypeDetector;
use Boson\Component\Http\Static\Mime\MimeTypeDetectorInterface;

final readonly class MimeTypeContentTypeDetector
{
    /**
     * @var list<non-empty-lowercase-string>
     */
    private const array KNOWN_TEXT_MIME_TYPES = [
        'application/xhtml+xml',
        'application/javascript',
        'application/json',
    ];

    /**
     * @var non-empty-lowercase-string
     */
    public const string DEFAULT_CHARSET = 'utf-8';

    /**
     * @var non-empty-lowercase-string
     */
    public const string DEFAULT_CONTENT_TYPE = 'text/html';

    public function __construct(
        /**
         * Contains default mime type for undetectable files.
         *
         * @var non-empty-string
         */
        private string $defaultMimeType = self::DEFAULT_CONTENT_TYPE,
        /**
         * Contains default charset for text files.
         *
         * @var non-empty-string
         */
        private string $defaultCharset = self::DEFAULT_CHARSET,
        /**
         * Contains mime type detector.
         */
        private MimeTypeDetectorInterface $mimeDetector = new ExtensionMimeTypeDetector(),
    ) {}

    /**
     * @param non-empty-string $pathname
     *
     * @return non-empty-string
     */
    public function findContentTypeByFile(string $pathname): string
    {
        /** @var non-empty-string $mimeType */
        $mimeType = $this->mimeDetector->findMimeTypeByFile($pathname)
            ?? $this->defaultMimeType;

        // Returns mime type with charset in case of file supports charset
        if (($charset = $this->getContentTypeCharset($mimeType)) !== null) {
            return $mimeType . '; ' . $charset;
        }

        return $mimeType;
    }

    /**
     * Returns default charset segment for content-type header value
     * by passed mime type value.
     *
     * @param non-empty-string $mimeType
     *
     * @return non-empty-string|null
     */
    private function getContentTypeCharset(string $mimeType): ?string
    {
        // Skip in case of charset already has been defined
        if (\str_contains($mimeType, 'charset=')) {
            return null;
        }

        if ($this->supportsContentType($mimeType)) {
            return 'charset=' . $this->defaultCharset;
        }

        return null;
    }

    /**
     * Returns {@see true} in case of passed mime type argument supports
     * charset definition.
     *
     * @param non-empty-string $mimeType
     */
    private function supportsContentType(string $mimeType): bool
    {
        return \str_starts_with($mimeType, 'text/')
            || \in_array($mimeType, self::KNOWN_TEXT_MIME_TYPES, true);
    }
}
