<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Tests;

use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\PathInterface;
use Boson\Contracts\Uri\Component\QueryInterface;
use Boson\Contracts\Uri\Component\SchemeInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;
use Boson\Contracts\Uri\UriInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;

/**
 * Note: Changing the behavior of these tests is allowed ONLY when updating
 *       a MAJOR version of the package.
 */
#[Group('boson-php/uri-contracts')]
final class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testUriInterfaceCompatibility(): void
    {
        new class implements UriInterface {
            public ?SchemeInterface $scheme {
                get {
                }
            }
            public ?AuthorityInterface $authority {
                get {
                }
            }
            public PathInterface $path {
                get {
                }
            }
            public QueryInterface $query {
                get {
                }
            }
            public ?string $fragment {
                get {
                }
            }

            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testSchemeInterfaceCompatibility(): void
    {
        new class implements SchemeInterface {
            public string $name {
                get {
                }
            }

            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testAuthorityInterfaceCompatibility(): void
    {
        new class implements AuthorityInterface {
            public ?UserInfoInterface $userInfo {
                get {
                }
            }
            public string $host {
                get {
                }
            }
            public ?int $port {
                get {
                }
            }

            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testPathInterfaceCompatibility(): void
    {
        new class implements PathInterface, \IteratorAggregate {
            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}

            public function count(): int {}

            public function getIterator(): \Traversable {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testQueryInterfaceCompatibility(): void
    {
        new class implements QueryInterface, \IteratorAggregate {
            public function has(string $name): bool {}

            public function get(string $name, ?string $default = null): ?string {}

            public function getAsInt(string $name, ?int $default = null): ?int {}

            public function getAsArray(string $name, array $default = []): array {}

            public function toArray(): array {}

            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}

            public function count(): int {}

            public function getIterator(): \Traversable {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testUserInfoInterfaceCompatibility(): void
    {
        new class implements UserInfoInterface {
            public string $username {
                get {
                }
            }
            public ?string $password {
                get {
                }
            }

            public function equals(mixed $other): bool {}

            public function __toString(): string {}

            public function toString(): string {}
        };
    }
}
