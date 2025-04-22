<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Authority;

use Boson\Http\Uri\AuthorityInterface;

/**
 * @phpstan-require-implements AuthorityInterface
 */
trait AuthorityStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public ?UserInfoInterface $userInfo { get; }
    // abstract public string $host { get; }
    // abstract public ?int $port { get; }
    //

    public function __toString(): string
    {
        $result = $this->host;

        if ($this->port !== null) {
            $result .= AuthorityInterface::HOST_PORT_DELIMITER . $this->port;
        }

        if ($this->userInfo !== null) {
            return $this->userInfo . AuthorityInterface::USER_INFO_DELIMITER . $result;
        }

        return $result;
    }
}
