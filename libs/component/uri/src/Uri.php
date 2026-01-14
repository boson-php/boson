<?php

declare(strict_types=1);

namespace Boson\Component\Uri;

use Boson\Component\Uri\Component\Path;
use Boson\Component\Uri\Component\Query;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\PathInterface;
use Boson\Contracts\Uri\Component\QueryInterface;
use Boson\Contracts\Uri\Component\SchemeInterface;
use Boson\Contracts\Uri\UriInterface;

final class Uri implements UriInterface
{
    /**
     * Gets the user component of the URI.
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->authority?->userInfo?->user;
    }

    /**
     * Gets the password component of the URI.
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get => $this->authority?->userInfo?->password;
    }

    /**
     * Gets the host component of the URI.
     *
     * @var non-empty-string|null
     */
    public ?string $host {
        get => $this->authority?->host;
    }

    /**
     * Gets the port component of the URI.
     *
     * @var int<0, 65535>|null
     */
    public ?int $port {
        get => $this->authority?->port;
    }

    public function __construct(
        public readonly PathInterface $path = new Path(),
        public readonly QueryInterface $query = new Query(),
        public readonly ?SchemeInterface $scheme = null,
        public readonly ?AuthorityInterface $authority = null,
        /**
         * @var non-empty-string|null
         */
        public readonly ?string $fragment = null,
    ) {}

    public function equals(mixed $other): bool
    {
        return $other === $this
            || (
                $other instanceof self
                && $other->fragment === $this->fragment
                && ($other->scheme === $this->scheme
                    || $other->scheme?->equals($this->scheme) === true)
                && ($other->authority === $this->authority
                    || $other->authority?->equals($this->authority) === true)
                && ($other->path === $this->path
                    || $other->path->equals($this->path) === true)
                && ($other->query === $this->query
                    || $other->query->equals($this->query) === true)
            );
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $result = '';

        if ($this->scheme !== null) {
            $result .= $this->scheme . ':';
        }

        if ($this->authority !== null) {
            $result .= '//' . $this->authority;
        }

        $result .= $this->path;

        if ($this->query->count() !== 0) {
            $result .= '?' . $this->query;
        }

        if ($this->fragment !== null) {
            $result .= '#' . \rawurlencode($this->fragment);
        }

        return $result;
    }
}
