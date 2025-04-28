<?php

declare(strict_types=1);

namespace Boson\Http\Headers\ContentDisposition;

interface ContentDispositionTypeInterface extends \Stringable
{
    /**
     * Gets value of content disposition type.
     *
     * The value must be case insensitive (i.e. lowercased).
     *
     * @link https://httpwg.org/specs/rfc6266.html
     * @link https://httpwg.org/specs/rfc6266.html#n-grammar
     *
     * @var non-empty-lowercase-string
     */
    public string $value { get; }

    /**
     * @return non-empty-lowercase-string
     */
    public function __toString(): string;
}
