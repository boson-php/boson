<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\UriInterface;
use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * Represents the path component of an {@see UriInterface}.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.3
 *
 * @template-extends \Traversable<array-key, non-empty-string>
 */
interface PathInterface extends StringValueObjectInterface, \Traversable, \Countable
{
    /**
     * Provides segment delimiter of the path component.
     *
     * @var non-empty-string
     */
    public const string SEGMENT_DELIMITER = '/';

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
