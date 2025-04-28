<?php

declare(strict_types=1);

namespace Boson\Http\Headers\HeaderLine;

use Boson\Http\Headers\HeaderLineInterface;

/**
 * @phpstan-require-implements HeaderLineInterface
 *
 * @internal this is an internal library trait, please do not use it in your code
 * @psalm-internal Boson\Http\Headers
 */
trait HeaderLineSemicolonDividedContentImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $value { get; }
    //

    /**
     * Gets header line semicolon-divided segments
     *
     * @var list<non-empty-string>
     */
    private array $segments {
        get {
            if (!isset($this->segments)) {
                $result = [];

                foreach (\explode(';', $this->rawHeaderValueString) as $segment) {
                    if (($trimmed = \trim($segment)) === '') {
                        continue;
                    }

                    $result[] = $trimmed;
                }

                $this->segments = $result;
            }

            return $this->segments;
        }
    }

    /**
     * @param non-empty-string $segment
     *
     * @return non-empty-string
     */
    protected function findSegmentValue(string $segment): ?string
    {
        foreach ($this->segments as $actual) {
            if (\str_starts_with($actual, $segment)) {
                $rawSegmentValue = \ltrim(\substr($actual, \strlen($segment)));

                if ($rawSegmentValue !== '') {
                    return $rawSegmentValue;
                }
            }
        }

        return null;
    }

    /**
     * @param non-empty-string $segment
     */
    protected function findDoubleQuotedSegmentValue(string $segment): ?string
    {
        $result = $this->findSegmentValue($segment);

        if ($result === null || \strlen($result) <= 1) {
            return null;
        }

        if (\str_starts_with($result, '"') && \str_ends_with($result, '"')) {
            return \substr($result, 1, -1);
        }

        return null;
    }

    /**
     * @param non-empty-string $segment
     *
     * @return non-empty-lowercase-string|null
     */
    protected function findSegmentValueAsLowercase(string $segment): ?string
    {
        $result = $this->findSegmentValue($segment);

        if ($result === null) {
            return null;
        }

        return \strtolower($result);
    }
}
