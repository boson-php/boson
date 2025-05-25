<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Component\Http\Body\MultipartFormData\FormDataBoundary;
use Boson\Component\Http\Body\MultipartFormData\StreamingParser;
use Boson\Contracts\Http\HeadersInterface;
use Boson\Contracts\Http\RequestInterface;

final readonly class MultipartFormDataDecoder implements SpecializedBodyDecoderInterface
{
    /**
     * @var non-empty-lowercase-string
     */
    private const string EXPECTED_CONTENT_TYPE = 'multipart/form-data';

    public function __construct(
        private StreamingParser $parser = new StreamingParser(),
    ) {}

    public function decode(RequestInterface $request): array
    {
        $boundary = FormDataBoundary::findFromRequest($request);

        if ($boundary === null) {
            return [];
        }

        $result = [];

        try {
            $elements = $this->parser->parse(
                stream: $this->requestToStream($request),
                boundary: $boundary,
            );

            while ($elements->valid()) {
                try {
                    $name = $this->getContentDispositionName($elements->key());

                    if ($name !== null) {
                        $result[$name] = $elements->current();
                    }
                } catch (\Throwable) {
                    // skip unprocessable body segment
                }

                $elements->next();
            }
        } catch (\Throwable) {
            /** @var array<non-empty-string, string> */
            return $result;
        }

        /** @var array<non-empty-string, string> */
        return $result;
    }

    /**
     * @return non-empty-string|null
     */
    private function getContentDispositionName(HeadersInterface $headers): ?string
    {
        $contentDisposition = $headers->first('content-disposition');

        // Content-Disposition header, that contain form element name is
        // required to correct body payload decoding.
        if ($contentDisposition === null) {
            return null;
        }

        $segments = \explode(';', $contentDisposition);

        // Allows only "form-data" content disposition type.
        if (!\in_array('form-data', $segments, true)) {
            return null;
        }

        foreach ($segments as $segment) {
            $trimmed = \trim($segment);

            // Expects only `name="xxxx"` sub-segment
            if (!\str_starts_with($trimmed, 'name="') || !\str_ends_with($trimmed, '"')) {
                continue;
            }

            $trimmed = \substr($trimmed, 6, -1);

            // Empty names not allowed
            if ($trimmed === '') {
                continue;
            }

            return $trimmed;
        }

        return null;
    }

    /**
     * @return resource
     */
    private function requestToStream(RequestInterface $request): mixed
    {
        $stream = \fopen('php://memory', 'rb+');

        if ($stream === false) {
            throw new \RuntimeException('Unable to open php://memory stream');
        }

        \fwrite($stream, $request->body);
        \rewind($stream);

        /** @var resource */
        return $stream;
    }

    public function isDecodable(RequestInterface $request): bool
    {
        $contentType = $request->headers->first('content-type');

        if ($contentType === null) {
            return false;
        }

        return $contentType === self::EXPECTED_CONTENT_TYPE
            || \str_starts_with($contentType, self::EXPECTED_CONTENT_TYPE);
    }
}
