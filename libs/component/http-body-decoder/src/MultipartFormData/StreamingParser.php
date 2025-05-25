<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

use Boson\Component\Http\Body\Exception\ParsingException;
use Boson\Component\Http\HeadersMap;
use Boson\Contracts\Http\HeadersInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final readonly class StreamingParser
{
    /**
     * Size of chunk for reading per I/O "tick".
     */
    public const int DEFAULT_CHUNKS_SIZE = 8 * 1024;

    /**
     * Provides internal stateless streaming headers parser
     * of the multipart element.
     */
    private FormDataHeadersParser $headersStream;

    /**
     * Provides internal stateless streaming body parser
     * of the multipart element.
     */
    private FormDataBodyParser $bodyStream;

    /**
     * @psalm-taint-sink file $tempDirectory
     * @param int<1, max> $chunkSize
     * @param int<1, max> $headersSize
     * @param int<1, max> $headersMaxCount
     * @param int<1, max>|null $bodyMaxSize
     */
    public function __construct(
        int $chunkSize = self::DEFAULT_CHUNKS_SIZE,
        int $headersSize = FormDataHeadersParser::DEFAULT_MAX_HEADER_SIZE,
        int $headersMaxCount = FormDataHeadersParser::DEFAULT_MAX_HEADERS_COUNT,
        ?int $bodyMaxSize = null,
    ) {
        $this->headersStream = new FormDataHeadersParser($chunkSize, $headersSize, $headersMaxCount);
        $this->bodyStream = new FormDataBodyParser($chunkSize, $bodyMaxSize);
    }

    /**
     * @param resource $stream
     * @param non-empty-string $boundary
     *
     * @return \Iterator<HeadersInterface, string>
     * @throws \Throwable
     */
    public function parse(mixed $stream, string $boundary): \Iterator
    {
        $finalized = false;

        do {
            $size = FormDataBoundary::isStarted($stream, $boundary);

            if ($size === 0) {
                throw ParsingException::becauseInvalidBody();
            }

            \fseek($stream, $size, \SEEK_CUR);

            yield $this->getHeaders($stream) => $this->getContent($stream, $boundary);

            if (FormDataBoundary::isFinal($stream, $boundary) > 0) {
                $finalized = true;
                break;
            }
        } while (!\feof($stream));

        if (!$finalized) {
            throw ParsingException::becauseEndlessBody();
        }
    }

    /**
     * @param resource $stream
     *
     * @throws \Throwable
     */
    private function getHeaders($stream): HeadersInterface
    {
        return HeadersMap::createFromIterable(
            headers: $this->headersStream->parse($stream),
        );
    }

    /**
     * @param resource $stream
     * @param non-empty-string $boundary
     *
     * @throws \Throwable
     */
    private function getContent($stream, string $boundary): string
    {
        $size = 0;

        /**
         * @var non-empty-string|null $file
         * @var resource $buffer
         */
        [$file, $buffer] = $this->createMemoryBuffer();

        foreach ($this->bodyStream->parse($stream, $boundary) as $offset => $chunk) {
            $size += \strlen($chunk);

            // In case of additional data (chunk), flush the buffer to
            // a temporary file and switch to writing to the file system.
            if ($file === null && $offset > 0) {
                // TODO It is necessary to implement a switch from memory to
                //      FS temp file in case of large volumes of payload.
                //      Not required at this moment:
                //      - See https://github.com/MicrosoftEdge/WebView2Feedback/issues/2162
                // [$file, $buffer] = $this->switchToFileBuffer($buffer);
            }

            \fwrite($buffer, $chunk);
        }

        \rewind($buffer);

        return (string) \stream_get_contents($buffer);
    }

    /**
     * @return array{null, resource}
     */
    private function createMemoryBuffer(): array
    {
        $result = @\fopen('php://memory', 'rb+');

        if ($result === false) {
            throw throw new \RuntimeException('Unable to open php://memory stream');
        }

        return [null, $result];
    }
}
