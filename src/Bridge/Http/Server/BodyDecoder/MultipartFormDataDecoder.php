<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server\BodyDecoder;

use Boson\Bridge\Http\Server\BodyDecoder\MultipartFormData\FormDataBoundary;
use Boson\Bridge\Http\Server\BodyDecoder\MultipartFormData\StreamingParser;
use Boson\Bridge\Http\Server\RequestTypeAwareDecoderInterface;
use Boson\Http\Headers\ContentDispositionHeader;
use Boson\Http\Headers\ContentTypeHeader;
use Boson\Http\RequestInterface;

/**
 * @template-implements RequestTypeAwareDecoderInterface<string|array<array-key, string>>
 */
final readonly class MultipartFormDataDecoder implements RequestTypeAwareDecoderInterface
{
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

            foreach ($elements as $headers => $content) {
                $contentDisposition = $headers->first('content-disposition');

                // Form data element must contain content disposition header
                if (!$contentDisposition instanceof ContentDispositionHeader) {
                    continue;
                }

                $contentDispositionName = $contentDisposition->name;

                // Form name cannot be empty
                if (!\is_string($contentDispositionName) || $contentDispositionName === '') {
                    continue;
                }

                $result[$contentDispositionName] = $content;
            }
        } catch (\Throwable) {
            return $result;
        }

        return $result;
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

    public function isSupports(RequestInterface $request): bool
    {
        $contentType = $request->headers->first('content-type');

        if ($contentType instanceof ContentTypeHeader) {
            return $contentType->mimeType === 'multipart/form-data';
        }

        return false;
    }
}
