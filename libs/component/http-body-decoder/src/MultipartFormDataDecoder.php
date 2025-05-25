<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body;

use Boson\Component\Http\Body\MultipartFormData\FormDataBoundary;
use Boson\Component\Http\Body\MultipartFormData\FormDataBoundaryFactory;
use Boson\Component\Http\MutableHeadersMap;
use Boson\Contracts\Http\HeadersInterface;
use Boson\Contracts\Http\RequestInterface;

final readonly class MultipartFormDataDecoder implements SpecializedBodyDecoderInterface
{
    /**
     * @var non-empty-lowercase-string
     */
    private const string EXPECTED_CONTENT_TYPE = 'multipart/form-data';

    private FormDataBoundaryFactory $boundary;

    public function __construct()
    {
        $this->boundary = new FormDataBoundaryFactory();
    }

    public function decode(RequestInterface $request): array
    {
        $boundary = $this->boundary->tryCreateFromContentType(
            header: (string) $request->headers->first('content-type'),
        );

        if ($boundary === null) {
            return [];
        }

        $iterator = $this->requestToIterator($request);

        $result = [];

        foreach ($this->iteratorToHeadersAndBodyPair($boundary, $iterator) as $headers => $body) {
            $name = $this->getContentDispositionName($headers);

            if ($name !== null) {
                $result[$name] = $body;
            }
        }

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
     * Transforms request object to body iterator.
     *
     * @return \Iterator<array-key, string>
     */
    private function requestToIterator(RequestInterface $request): \Iterator
    {
        $buffer = $request->body;
        $offset = $next = 0;

        while ($next !== false) {
            $next = \strpos($buffer, "\r\n", $offset);

            if ($next === false) {
                yield \substr($buffer, $offset);

                return;
            }

            yield \substr($buffer, $offset, $next - $offset);

            $offset = $next + 2;
        }
    }

    /**
     * Transforms iterator to headers + body key-value pairs
     *
     * @param \Iterator<mixed, string> $iterator
     *
     * @return iterable<HeadersInterface, string>
     */
    private function iteratorToHeadersAndBodyPair(FormDataBoundary $boundary, \Iterator $iterator): iterable
    {
        $body = '';
        $headers = null;

        while ($iterator->valid()) {
            $chunk = $iterator->current();

            switch ($chunk) {
                // In case of start next segment
                case $boundary->segment:
                    // Flush segment pair
                    if ($headers !== null) {
                        yield $headers => $body;
                        $headers = null;
                    }

                    $iterator->next();
                    $headers = $this->nextHeadersFromIterator($iterator);
                    continue 2;
                case $boundary->end:
                    break 2;

                case '':
                    $iterator->next();
                    $body = $this->nextBodyFromIterator($boundary, $iterator);
                    continue 2;
            }

            $iterator->next();
        }

        // Flush segment pair
        if ($headers !== null) {
            yield $headers => $body;
        }
    }

    /**
     * @param \Iterator<mixed, string> $iterator
     */
    private function nextBodyFromIterator(FormDataBoundary $boundary, \Iterator $iterator): string
    {
        $result = '';

        while ($iterator->valid()) {
            $chunk = $iterator->current();

            // Complete body decoding at any boundary delimiter
            if ($chunk === $boundary->segment || $chunk === $boundary->end) {
                break;
            }

            if ($result !== '') {
                $result .= "\r\n";
            }

            $result .= $chunk;

            $iterator->next();
        }

        return $result;
    }

    /**
     * @param \Iterator<mixed, string> $iterator
     */
    private function nextHeadersFromIterator(\Iterator $iterator): HeadersInterface
    {
        $headers = new MutableHeadersMap();

        while ($iterator->valid()) {
            $headerLine = $iterator->current();

            // Complete headers decoding at empty line ("\r\n") delimiter
            if ($headerLine === '') {
                break;
            }

            $iterator->next();

            $headerValueStartsAt = \strpos($headerLine, ':');

            // Header line should contain `:` char
            if ($headerValueStartsAt === false || $headerValueStartsAt === 0) {
                continue;
            }

            $headerName = \substr($headerLine, 0, $headerValueStartsAt);
            $headerValue = \substr($headerLine, $headerValueStartsAt + 1);

            $headers->add($headerName, \ltrim($headerValue));
        }

        return $headers;
    }

    public function isDecodable(RequestInterface $request): bool
    {
        $contentType = $request->headers->first('content-type');

        if ($contentType === null || !\str_starts_with($contentType, self::EXPECTED_CONTENT_TYPE)) {
            return false;
        }

        return $this->boundary->tryCreateFromContentType($contentType) !== null;
    }
}
