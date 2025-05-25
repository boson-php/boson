<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

use Boson\Component\Http\Body\Exception\OutOfMemoryException;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final readonly class FormDataHeadersParser
{
    /**
     * Size of chunk for reading per I/O "tick".
     */
    public const int DEFAULT_CHUNKS_SIZE = StreamingParser::DEFAULT_CHUNKS_SIZE;

    /**
     * Limit on the number of allowed headers for a form-data
     * body element.
     *
     * In most cases, there can be no more than three:
     * - content-disposition
     * - content-type
     * - content-transfer-encoding
     *
     * ```
     *  --BoundaryAaB03x
     *  content-disposition: form-data; name="field1"
     *  content-type: text/plain; charset=utf-8
     *  content-transfer-encoding: quoted-printable
     *
     *  example text
     * ```
     */
    public const int DEFAULT_MAX_HEADERS_COUNT = 10;

    /**
     * Most web servers have their own set of size limits on HTTP request headers.
     * The HTTP Header values are restricted by server implementations. The following
     * are the limits of some of the most popular web servers:
     *
     * - Apache: 8K
     * - Nginx: 4K-8K
     * - IIS: 8K-16K
     * - Tomcat: 8K – 48K
     *
     * In this case, we use the maximum (by default) allowed value of nginx.
     */
    public const int DEFAULT_MAX_HEADER_SIZE = 8 * 1024;

    /**
     * @param int<1, max> $chunkSize
     * @param int<1, max> $maxSize
     * @param int<1, max> $maxCount
     */
    public function __construct(
        private int $chunkSize = self::DEFAULT_CHUNKS_SIZE,
        private int $maxSize = self::DEFAULT_MAX_HEADER_SIZE,
        private int $maxCount = self::DEFAULT_MAX_HEADERS_COUNT,
    ) {
        assert($this->chunkSize > 0, 'Chunk size must be greater than 0');
        assert($this->maxSize > 0, 'Buffer size must be greater than 0');
        assert($this->maxCount > 0, 'Headers count must be greater than 0');

        assert(
            $chunkSize <= $this->maxSize,
            'Buffer size should be greater or equals than chunk size'
        );
    }

    /**
     * @param resource $stream
     *
     * @return iterable<non-empty-string, string>
     * @throws \Throwable
     */
    public function parse($stream): iterable
    {
        if (!\is_resource($stream)) {
            return;
        }

        $position = 0;

        while (!\feof($stream)) {
            $headerLine = $this->readLine($stream);

            // 1) Stop on header completion
            // 2) Exclude infinite loop
            if ($headerLine === "\r\n" || \ftell($stream) === $position) {
                break;
            }

            if ($headerLine === '') {
                continue;
            }

            [$header, $value] = $this->splitHeaderLine($headerLine);

            if ($header !== '') {
                yield $header => $value;
            }

            $position = \ftell($stream);
        }
    }

    /**
     * Converts header line like "Content-Type: application/json" into PHP
     * array ["content-type", "application/json"]
     *
     * @param non-empty-string $headerLine
     *
     * @return array{string, string}
     */
    private function splitHeaderLine(string $headerLine): array
    {
        $parts = \explode(':', \rtrim($headerLine));

        return [
            \strtolower(\array_shift($parts)),
            \ltrim(\implode(':', $parts)),
        ];
    }

    /**
     * @param resource $stream
     *
     * @throws \Throwable
     */
    private function readLine($stream): string
    {
        [$buffer, $length, $position] = ['', 0, 0];

        do {
            $buffer .= $chunk = (string) \fgets($stream, $this->chunkSize);

            // Exclude infinite loop
            if (\ftell($stream) === $position) {
                break;
            }

            $length += \strlen($chunk);

            if ($length > $this->maxSize) {
                throw OutOfMemoryException::becauseMemoryLimitOverflow($length, $this->maxSize);
            }

            $position = \ftell($stream);
        } while (!\str_ends_with($chunk, "\r\n") && !\feof($stream));

        return $buffer;
    }
}
