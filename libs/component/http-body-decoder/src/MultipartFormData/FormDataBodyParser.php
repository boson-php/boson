<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

use Boson\Component\Http\Body\Exception\OutOfMemoryException;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final readonly class FormDataBodyParser
{
    /**
     * Size of chunk for reading per I/O "tick".
     */
    public const int DEFAULT_CHUNKS_SIZE = StreamingParser::DEFAULT_CHUNKS_SIZE;

    /**
     * Default size of the body (in bytes).
     *
     * The {@see null} value means unlimited and is controlled
     * exclusively by the front-server (nginx) and php.ini value (post_max_size).
     *
     * @link https://www.php.net/manual/ru/ini.core.php#ini.post-max-size
     */
    public const ?int DEFAULT_MAX_BODY_SIZE = null;

    /**
     * @param int<1, max> $chunkSize
     * @param int<1, max>|null $maxSize
     */
    public function __construct(
        private int $chunkSize = self::DEFAULT_CHUNKS_SIZE,
        private ?int $maxSize = self::DEFAULT_MAX_BODY_SIZE,
    ) {
        assert($this->chunkSize > 0, 'Chunk size must be greater than 0');
        assert(
            $this->maxSize === null || $this->maxSize <= $this->chunkSize,
            'Body size should be greater or equals than chunk size'
        );
    }

    /**
     * @param resource $stream
     * @param non-empty-string $boundary
     *
     * @return iterable<int<0, max>, non-empty-string>
     */
    public function parse(mixed $stream, string $boundary): iterable
    {
        if (!\is_resource($stream)) {
            return;
        }

        $length = 0;
        $previousOffset = 0;

        do {
            $chunk = (string) \fgets($stream, $this->chunkSize);

            $size = \strlen($chunk);
            $length += $size;
            $current = \ftell($stream);

            // In the case that the data transfer ended unexpectedly and there
            // is no opening or closing binder at the end, then the reading may loop.
            if ($previousOffset === $current) {
                break;
            }

            $previousOffset = $current;

            if ($this->maxSize !== null && $length > $this->maxSize) {
                throw OutOfMemoryException::becauseMemoryLimitOverflow($length, $this->maxSize);
            }

            $completed = $this->isComplete($stream, $chunk, $boundary);

            if ($completed) {
                $chunk = \substr($chunk, 0, -2);
            }

            if ($chunk !== '') {
                /** @phpstan-ignore-next-line */
                yield $length - $size => $chunk;
            }
        } while (!\feof($stream) && !$completed);
    }

    /**
     * @param resource $stream
     * @param non-empty-string $boundary
     */
    private function isComplete(mixed $stream, string $chunk, string $boundary): bool
    {
        if (!\str_ends_with($chunk, "\r\n")) {
            return false;
        }

        return FormDataBoundary::isStarted($stream, $boundary) > 0
            || FormDataBoundary::isFinal($stream, $boundary) > 0;
    }
}
