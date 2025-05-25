<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

/**
 * Factory class for creating {@see FormDataBoundary} DTO instances from
 * `content-type` headers.
 *
 * This class is responsible for parsing the boundary parameter from
 * `content-type` headers in `multipart/xxx` requests.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final class FormDataBoundaryFactory
{
    /**
     * Attempts to create a {@see FormDataBoundary} instance from a
     * `content-type` header string value
     */
    public function tryCreateFromContentType(string $header): ?FormDataBoundary
    {
        // Lookup for "boundary=" prefix
        // @link https://www.rfc-editor.org/rfc/rfc2046.html#section-5.1.1
        $startsAt = \strpos($header, 'boundary=');

        if ($startsAt === false) {
            return null;
        }

        // Go to boundary payload value
        $boundaryPayload = \substr($header, $startsAt + 9);

        if ($boundaryPayload === '' || $boundaryPayload === '""') {
            return null;
        }

        // In case of boundary value contain `boundary=XYZ` format
        if ($boundaryPayload[0] !== '"') {
            // Lookup for trailing `;`
            $endsWith = \strpos($boundaryPayload, ';');

            // In case of boundary is last header segment: `boundary=value`
            $boundaryPayload = \trim($endsWith === false
                ? $boundaryPayload
                : \substr($boundaryPayload, 0, $endsWith));

            if ($boundaryPayload === '') {
                return null;
            }

            // In case of boundary is last header segment: `boundary=value; other=42`
            return new FormDataBoundary($boundaryPayload);
        }

        // Lookup for trailing `"`
        $endsWith = \strpos($boundaryPayload, '"', 1);

        // In case of boundary is not quote-closed: `boundary="test`
        if ($endsWith === false) {
            return null;
        }

        $boundaryPayload = \trim(\substr($boundaryPayload, 1, $endsWith - 1));

        if ($boundaryPayload === '') {
            return null;
        }

        return new FormDataBoundary($boundaryPayload);
    }
}
