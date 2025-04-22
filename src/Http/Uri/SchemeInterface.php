<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\UriInterface;
use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * Represents the scheme component of an {@see UriInterface}.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.1
 */
interface SchemeInterface extends StringValueObjectInterface
{
    /**
     * Each {@see UriInterface URI} begins with a scheme name that refers to
     * a specification for assigning identifiers within that scheme. As such,
     * the URI syntax is a federated and extensible naming system wherein
     * each scheme's specification may further restrict the syntax and
     * semantics of identifiers using that scheme.
     *
     * Scheme names consist of a sequence of characters beginning with a
     * letter and followed by any combination of letters, digits, plus
     * ("+"), period ("."), or hyphen ("-"). Although schemes are case-
     * insensitive, the canonical form is lowercase and documents that
     * specify schemes must do so with lowercase letters. An implementation
     * should accept uppercase letters as equivalent to lowercase in scheme
     * names (e.g., allow "HTTP" as well as "http") for the sake of
     * robustness but should only produce lowercase scheme names for
     * consistency.
     *
     * ```
     * scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
     * ```
     *
     * @var non-empty-lowercase-string
     */
    public string $name { get; }

    /**
     * @return non-empty-lowercase-string
     */
    public function __toString(): string;
}
