<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\UriInterface;

/**
 * @phpstan-require-implements UriInterface
 */
trait UriStringableImpl
{
    /**
     * @var non-empty-string
     */
    private const string SCHEMA_SUFFIX = ':';

    /**
     * @var non-empty-string
     */
    private const string AUTHORITY_PREFIX = '//';

    /**
     * @var non-empty-string
     */
    private const string AUTHORITY_SUFFIX = '/';

    /**
     * @var non-empty-string
     */
    private const string QUERY_PREFIX = '?';

    /**
     * @var non-empty-string
     */
    private const string FRAGMENT_PREFIX = '#';

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public ?SchemeInterface $scheme { get; }
    // abstract public ?AuthorityInterface $authority { get; }
    // abstract public PathInterface $path { get; }
    // abstract QueryInterface $query { get; }
    // abstract ?string $fragment { get; }
    //

    public function __toString(): string
    {
        $result = '';

        if ($this->scheme !== null) {
            $result .= $this->scheme . self::SCHEMA_SUFFIX;
        }

        if ($this->authority !== null) {
            $result .= self::AUTHORITY_PREFIX . $this->authority . self::AUTHORITY_SUFFIX;
        }

        $result .= $this->path;

        if ($this->query->count() !== 0) {
            $result .= self::QUERY_PREFIX . $this->query;
        }

        if ($this->fragment !== null) {
            $result .= self::FRAGMENT_PREFIX . \urlencode($this->fragment);
        }

        return $result;
    }
}
