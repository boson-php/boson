<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\Headers\HeaderLine\HeaderLineSemicolonDividedContentImpl;

/**
 * The HTTP Content-Type representation header is used to indicate the original
 * media type of a resource before any content encoding is applied.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Type
 */
final class ContentTypeHeader extends HeaderLine
{
    use HeaderLineSemicolonDividedContentImpl;

    /**
     * Contain content-type's mime type for non-decodable header lines
     *
     * @var non-empty-lowercase-string
     */
    private const string DEFAULT_UNKNOWN_CONTENT_TYPE = 'application/octet-stream';

    /**
     * Gets mime type of the content type header.
     *
     * @link https://www.iana.org/assignments/media-types/media-types.xhtml
     * @link https://github.com/chromium/chromium/blob/137.0.7122.1/net/base/mime_util.cc#L95-L255
     *
     * @var non-empty-lowercase-string
     */
    public string $mimeType {
        get => $this->mimeType ??= \strtolower(
            string: $this->segments[0] ?? self::DEFAULT_UNKNOWN_CONTENT_TYPE,
        );
    }

    /**
     * Indicates the character encoding standard used.
     *
     * The value is case insensitive but lowercase is preferred.
     *
     * @var non-empty-lowercase-string|null
     */
    public ?string $charset {
        get => $this->charset ??= $this->findSegmentValueAsLowercase('charset=');
    }

    /**
     * For multipart entities, the `boundary` parameter is required.
     *
     * It is used to demarcate the boundaries of the multiple parts of the
     * message. The value consists of 1 to 70 characters (not ending with white
     * space) known to be robust in the context of different systems (e.g.,
     * email gateways). Often, the header boundary is prepended by two dashes
     * in the request body, and the final boundary has two dashes appended
     * at the end.
     *
     * @var non-empty-string|null
     */
    public ?string $boundary {
        get => $this->boundary ??= $this->findSegmentValue('boundary=');
    }
}
