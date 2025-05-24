<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Tests;

use Boson\Contracts\Http\Body\BodyProviderInterface;
use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\HeadersProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\HeadersInterface;
use Boson\Contracts\Http\Method\MethodProviderInterface;
use Boson\Contracts\Http\Method\MutableMethodProviderInterface;
use Boson\Contracts\Http\MutableHeadersInterface;
use Boson\Contracts\Http\MutableRequestInterface;
use Boson\Contracts\Http\MutableResponseInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Contracts\Http\StatusCode\MutableStatusCodeProviderInterface;
use Boson\Contracts\Http\StatusCode\StatusCodeProviderInterface;
use Boson\Contracts\Http\Url\MutableUrlProviderInterface;
use Boson\Contracts\Http\Url\UrlProviderInterface;
use PHPUnit\Framework\Attributes\Group;

/**
 * Note: Changing the behavior of these tests is allowed ONLY when updating
 *       a MAJOR version of the package.
 */
#[Group('boson-php/http-contracts')]
final class CompatibilityTest extends TestCase
{
    public function testBodyProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements BodyProviderInterface {
            public string $body {
                get => $value;
            }
        };
    }

    public function testMutableBodyProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableBodyProviderInterface {
            public string $body {
                get => $value;
                set(string|\Stringable $value) => $value;
            }
        };
    }

    public function testHeadersProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements HeadersProviderInterface {
            public HeadersInterface $headers {
                get => $value;
            }
        };
    }

    public function testMutableHeadersProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableHeadersProviderInterface {
            public MutableHeadersInterface $headers {
                get => $value;
                set(iterable $headers) => $headers;
            }
        };
    }

    public function testMethodProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MethodProviderInterface {
            public string $method {
                get => $value;
            }
        };
    }

    public function testMutableMethodProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableMethodProviderInterface {
            public string $method {
                get => $value;
                set(string|\Stringable $method) => $method;
            }
        };
    }

    public function testStatusCodeProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements StatusCodeProviderInterface {
            public int $status {
                get => $value;
            }
        };
    }

    public function testMutableStatusCodeProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableStatusCodeProviderInterface {
            public int $status {
                get => $value;
                set(int $status) => $status;
            }
        };
    }

    public function testUrlProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements UrlProviderInterface {
            public string $url {
                get => $value;
            }
        };
    }

    public function testMutableUrlProviderCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableUrlProviderInterface {
            public string $url {
                get => $value;
                set(string|\Stringable $url) => $url;
            }
        };
    }

    public function testHeadersCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements HeadersInterface, \IteratorAggregate {
            public function first(string $name, ?string $default = null): ?string {}
            public function all(string $name): array {}
            public function has(string $name): bool {}
            public function contains(string $name, string $value): bool {}
            public function count(): int {}

            public function getIterator(): \Traversable {}
        };
    }

    public function testMutableHeadersCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableHeadersInterface, \IteratorAggregate {
            public function first(string $name, ?string $default = null): ?string {}
            public function all(string $name): array {}
            public function has(string $name): bool {}
            public function contains(string $name, string $value): bool {}
            public function count(): int {}

            public function getIterator(): \Traversable {}

            public function set(string $name, string $value): void {}
            public function add(string $name, string $value): void {}
            public function remove(string $name): void {}
            public function removeAll(): void {}
        };
    }

    public function testRequestCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements RequestInterface {
            public string $body {
                get => $value;
            }

            public HeadersInterface $headers {
                get => $value;
            }

            public string $method {
                get => $value;
            }

            public string $url {
                get => $value;
            }
        };
    }

    public function testMutableRequestCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableRequestInterface {
            public string $body {
                get => $value;
                set(string|\Stringable $value) => $value;
            }

            public MutableHeadersInterface $headers {
                get => $value;
                set(iterable $headers) => $headers;
            }

            public string $method {
                get => $value;
                set(string|\Stringable $method) => $method;
            }

            public string $url {
                get => $value;
                set(string|\Stringable $url) => $url;
            }
        };
    }

    public function testResponseCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements ResponseInterface {
            public string $body {
                get => $value;
            }

            public HeadersInterface $headers {
                get => $value;
            }

            public int $status {
                get => $value;
            }
        };
    }

    public function testMutableResponseCompatibility(): void
    {
        self::expectNotToPerformAssertions();

        new class implements MutableResponseInterface {
            public string $body {
                get => $value;
                set(string|\Stringable $value) => $value;
            }

            public MutableHeadersInterface $headers {
                get => $value;
                set(iterable $headers) => $headers;
            }

            public int $status {
                get => $value;
                set(int $status) => $status;
            }
        };
    }
}
