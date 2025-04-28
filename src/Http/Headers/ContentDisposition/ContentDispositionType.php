<?php

declare(strict_types=1);

namespace Boson\Http\Headers\ContentDisposition;

use Boson\Shared\ValueObject\BackedEnumLikeImpl;

/**
 * @internal this constant cannot be autoloaded, please use {@see ContentDispositionType::Inline} instead
 */
const CONTENT_DISPOSITION_TYPE_INLINE = new StandardContentDispositionType('inline');

/**
 * @internal this constant cannot be autoloaded, please use {@see ContentDispositionType::Attachment} instead
 */
const CONTENT_DISPOSITION_TYPE_ATTACHMENT = new StandardContentDispositionType('attachment');

/**
 * @internal this constant cannot be autoloaded, please use {@see ContentDispositionType::FormData} instead
 */
const CONTENT_DISPOSITION_TYPE_FORM_DATA = new StandardContentDispositionType('form-data');

final class ContentDispositionType implements ContentDispositionTypeInterface
{
    /** @use BackedEnumLikeImpl<ContentDispositionTypeInterface, non-empty-lowercase-string> */
    use BackedEnumLikeImpl;

    /**
     * If the disposition type matches 'attachment' (case-insensitively), this
     * indicates that the recipient should prompt the user to save the response
     * locally, rather than process it normally (as per its media type).
     *
     * @api
     *
     * @link https://httpwg.org/specs/rfc6266.html#disposition.type
     */
    public const StandardContentDispositionType Attachment = CONTENT_DISPOSITION_TYPE_ATTACHMENT;

    /**
     * If the disposition type matches 'inline' (case-insensitively), this
     * implies default processing. Therefore, the disposition type 'inline'
     * is only useful when it is augmented with additional parameters,
     * such as the filename (see below).
     *
     * @api
     *
     * @link https://httpwg.org/specs/rfc6266.html#disposition.type
     */
    public const StandardContentDispositionType Inline = CONTENT_DISPOSITION_TYPE_INLINE;

    /**
     * @link https://www.rfc-editor.org/rfc/rfc2388.html
     */
    public const StandardContentDispositionType FormData = CONTENT_DISPOSITION_TYPE_FORM_DATA;

    /**
     * @var non-empty-lowercase-string
     */
    public readonly string $value;

    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        $this->value = \strtolower($value);
    }

    /**
     * @return non-empty-lowercase-string
     */
    protected static function caseKeyFor(object $case): string
    {
        assert($case instanceof ContentDispositionTypeInterface);

        return $case->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
