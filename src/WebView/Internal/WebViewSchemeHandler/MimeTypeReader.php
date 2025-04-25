<?php

declare(strict_types=1);

namespace Boson\WebView\Internal\WebViewSchemeHandler;

use Boson\Http\ResponseInterface;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView\Internal
 */
final readonly class MimeTypeReader
{
    /**
     * Response header name with mime type
     */
    private const string CONTENT_TYPE_HEADER  = 'content-type';

    /**
     * Default mime type
     */
    private const string DEFAULT_MIME_TYPE = 'text/html';

    /**
     * @return non-empty-string|null
     */
    public function findFromResponse(ResponseInterface $response): ?string
    {
        foreach ($this->getAllFromResponse($response) as $mimeType) {
            return $mimeType;
        }

        return null;
    }

    /**
     * @param non-empty-string $default
     * @return non-empty-string
     */
    public function getFromResponse(ResponseInterface $response, string $default = self::DEFAULT_MIME_TYPE): string
    {
        foreach ($this->getAllFromResponse($response) as $mimeType) {
            return $mimeType;
        }

        return $default;
    }

    /**
     * @return iterable<array-key, non-empty-string>
     */
    private function getAllFromResponse(ResponseInterface $response): iterable
    {
        foreach ($response->headers[self::CONTENT_TYPE_HEADER] as $line) {
            $result = $this->getFromContentType($line);

            if (\str_contains($result, '/')) {
                yield $result;
            }
        }
    }

    private function getFromContentType(string $contentType): string
    {
        if (($offset = \strpos($contentType, ';')) !== false) {
            return \trim(\substr($contentType, 0, $offset));
        }

        return $contentType;
    }
}
