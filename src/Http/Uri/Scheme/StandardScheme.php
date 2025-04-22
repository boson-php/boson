<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Scheme;

use Boson\Http\Uri\SchemeInterface;

/**
 * An implementation of the standard HTTP schemes.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Http\Uri
 */
final readonly class StandardScheme implements SchemeInterface
{
    use SchemeValueObjectImpl;

    /**
     * @var non-empty-lowercase-string
     */
    public string $name;

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        /**
         * Gets default (known) port of the standard scheme (if available).
         *
         * @var int<0, 65535>|null
         */
        public ?int $port = null,
    ) {
        $this->name = \strtolower($name);
    }
}
