<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\Headers\ContentDisposition\ContentDispositionType;
use Boson\Http\Headers\ContentDisposition\ContentDispositionTypeInterface;
use Boson\Http\Headers\HeaderLine\HeaderLineSemicolonDividedContentImpl;

/**
 * The HTTP Content-Disposition header indicates whether content should be
 * displayed inline in the browser as a web page or part of a web page
 * or downloaded as an attachment locally.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Disposition
 */
final class ContentDispositionHeader extends HeaderLine
{
    use HeaderLineSemicolonDividedContentImpl;

    /**
     * Gets content disposition type or {@see null} in case
     * of value is unsupported.
     */
    public ?ContentDispositionTypeInterface $type {
        get {
            return $this->type ??= match ($typeScalarValue = ($this->segments[0] ?? null)) {
                'inline' => ContentDispositionType::Inline,
                'attachment' => ContentDispositionType::Attachment,
                'form-data' => ContentDispositionType::FormData,
                null => null,
                default => new ContentDispositionType($typeScalarValue),
            };
        }
    }

    /**
     * Is followed by a string containing the name of the HTML field in the
     * form that the content of this subpart refers to. When dealing with
     * multiple files in the same field (for example, the multiple attribute
     * of an <input type='file'> element), there can be several subparts
     * with the same name.
     *
     * A name with a value of '_charset_' indicates that the part is not an
     * HTML field, but the default charset to use for parts without
     * explicit charset information.
     */
    public ?string $name {
        get => $this->name ??= $this->findDoubleQuotedSegmentValue('name=');
    }

    /**
     * Is followed by a string containing the original name of the file
     * transmitted. This parameter provides mostly indicative information.
     *
     * The suggestions in RFC2183 apply:
     *
     * - Prefer ASCII characters if possible (the client may percent-encode
     *   it, as long as the server implementation decodes it).
     * - Any path information should be stripped, such as by replacing
     *   `/` with `_`.
     * - When writing to disk, it should not overwrite an existing file.
     * - Avoid creating special files with security implications, such
     *   as creating a file on the command search path.
     * - Satisfy other file system requirements, such as restricted characters
     *   and length limits.
     *
     * @link https://www.rfc-editor.org/rfc/rfc2183#section-2.3
     */
    public ?string $filename {
        get => $this->filename ??= $this->findDoubleQuotedSegmentValue('filename=');
    }
}
