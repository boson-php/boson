<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

final class FormDataBoundaryFactory
{
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
            if ($endsWith === false) {
                return new FormDataBoundary($boundaryPayload);
            }

            // In case of boundary is last header segment: `boundary=value; other=42`
            return new FormDataBoundary(\substr($boundaryPayload, 0, $endsWith));
        }

        // Lookup for trailing `"`
        $endsWith = \strpos($boundaryPayload, '"', 1);

        // In case of boundary is not quote-closed: `boundary="test`
        if ($endsWith === false) {
            return null;
        }

        return new FormDataBoundary(\substr($boundaryPayload, 1, $endsWith - 1));
    }
}
