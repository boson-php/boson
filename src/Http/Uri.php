<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Uri\AuthorityInterface;
use Boson\Http\Uri\PathInterface;
use Boson\Http\Uri\Query;
use Boson\Http\Uri\QueryInterface;
use Boson\Http\Uri\SchemeInterface;
use Boson\Http\Uri\UriValueObjectImpl;

final class Uri implements UriInterface
{
    use UriValueObjectImpl;

    /**
     * Gets the user component of the URI.
     *
     * @uses \Boson\Http\Uri\UserInfoInterface::$user
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->authority?->userInfo?->user;
    }

    /**
     * Gets the password component of the URI.
     *
     * @uses \Boson\Http\Uri\UserInfoInterface::$password
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get => $this->authority?->userInfo?->password;
    }

    /**
     * Gets the host component of the URI.
     *
     * @uses \Boson\Http\Uri\AuthorityInterface::$host
     *
     * @var non-empty-string|null
     */
    public ?string $host {
        get => $this->authority?->host;
    }

    /**
     * Gets the port component of the URI.
     *
     * @uses \Boson\Http\Uri\AuthorityInterface::$port
     *
     * @var int<0, 65535>|null
     */
    public ?int $port {
        get => $this->authority?->port;
    }

    public function __construct(
        public readonly PathInterface $path,
        public readonly QueryInterface $query = new Query(),
        public readonly ?SchemeInterface $scheme = null,
        public readonly ?AuthorityInterface $authority = null,
        /**
         * @var non-empty-string|null
         */
        public readonly ?string $fragment = null,
    ) {}
}
