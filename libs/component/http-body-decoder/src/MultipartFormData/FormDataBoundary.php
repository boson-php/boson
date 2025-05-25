<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

/**
 * DTO that represents a boundary in `multipart/xxxx` requests.
 *
 * If the boundary value is 'WebKitFormBoundary7MA4YWxkTrZu0gW', then:
 *  - `$segment` will be '--WebKitFormBoundary7MA4YWxkTrZu0gW'
 *  - `$end` will be '--WebKitFormBoundary7MA4YWxkTrZu0gW--'
 *
 * @link https://www.rfc-editor.org/rfc/rfc2046.html#section-5.1.1
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final readonly class FormDataBoundary
{
    /**
     * The boundary segment used to separate parts in the multipart form data.
     * This is the boundary value prefixed with `--`.
     *
     * @var non-empty-string
     */
    public string $segment;

    /**
     * The final boundary marking the end of the multipart form data.
     * This is the boundary value prefixed and suffixed with `--`.
     *
     * @var non-empty-string
     */
    public string $end;

    public function __construct(
        /**
         * The raw boundary value from the `content-type` header
         *
         * @var non-empty-string
         */
        public string $value,
    ) {
        $this->segment = '--' . $this->value;
        $this->end = '--' . $this->value . '--';
    }
}
