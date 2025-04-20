<?php

declare(strict_types=1);

namespace Boson\Internal;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class ThreadsCountResolver
{
    /**
     * Threads min count value.
     */
    private const int THREADS_MIN_COUNT = 1;

    /**
     * Threads max count value.
     */
    private const int THREADS_MAX_COUNT = 32767;

    /**
     * @return int<1, 32767>|null
     */
    public static function resolve(?int $threads): ?int
    {
        if ($threads === null) {
            return null;
        }

        self::assertValidMinThreadsCountBound($threads);
        self::assertValidMaxThreadsCountBound($threads);

        return $threads;
    }

    /**
     * @return ($threads is int<1, max> ? void : never)
     */
    private static function assertValidMinThreadsCountBound(int $threads): void
    {
        if ($threads >= self::THREADS_MIN_COUNT) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Threads count cannot be less than %d, but %d passed',
            self::THREADS_MIN_COUNT,
            $threads,
        ));
    }

    /**
     * @return ($threads is int<min, 32767> ? void : never)
     */
    private static function assertValidMaxThreadsCountBound(int $threads): void
    {
        if ($threads <= self::THREADS_MAX_COUNT) {
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Threads count cannot be greater than %d, but %d passed',
            self::THREADS_MAX_COUNT,
            $threads,
        ));
    }
}
