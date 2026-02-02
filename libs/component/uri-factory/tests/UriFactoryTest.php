<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Factory\Tests;

use Boson\Component\Uri\Factory\Component\UriPathFactory;
use Boson\Component\Uri\Factory\Component\UriQueryFactory;
use Boson\Component\Uri\Factory\Component\UriSchemeFactory;
use Boson\Component\Uri\Factory\Exception\InvalidUriException;
use Boson\Component\Uri\Factory\UriFactory;
use Boson\Component\Uri\Uri;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;
use Boson\Component\Uri\Factory\Exception\InvalidUriSchemeComponentException;
use Boson\Contracts\Uri\Component\SchemeInterface;
use Boson\Contracts\Uri\Factory\Component\UriSchemeFactoryInterface;

#[Group('boson-php/uri-factory')]
final class UriFactoryTest extends TestCase
{
    private UriFactory $factory;

    #[Before]
    protected function createPathFactory(): void
    {
        $this->factory = new UriFactory();
    }

    public function testCreateUriFromStringWithFullUri(): void
    {
        $uri = $this->factory->createUriFromString('https://user:pass@host:8080/path/to/resource?foo=bar#frag');

        self::assertSame('https', $uri->scheme?->toString());
        self::assertSame('host', $uri->authority?->host);
        self::assertSame(8080, $uri->authority?->port);
        self::assertSame('user', $uri->authority?->userInfo?->username);
        self::assertSame('pass', $uri->authority?->userInfo?->password);
        self::assertSame('/path/to/resource', $uri->path->toString());
        self::assertSame('foo=bar', $uri->query->toString());
        self::assertSame('frag', $uri->fragment);
    }

    public function testCreateUriFromStringWithMinimalUri(): void
    {
        $uri = $this->factory->createUriFromString('/');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('/', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithInvalidUri(): void
    {
        $uri = $this->factory->createUriFromString('!@#$%^&*()');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('%21%40', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertSame('$%^&*()', $uri->fragment);
    }

    public function testCreateUriFromStringWithEmptyScheme(): void
    {
        $uri = $this->factory->createUriFromString('://example.com/path');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('%3A/example.com/path', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithEmptySchemeStringThrowsException(): void
    {
        $schemeFactory = new UriSchemeFactory();

        $this->expectException(InvalidUriSchemeComponentException::class);
        $this->expectExceptionMessage('URI scheme cannot be empty');

        $schemeFactory->createSchemeFromString('');
    }

    public function testCreateUriFromStringWithStringableObjectThrowingException(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                throw new \RuntimeException('String casting failed');
            }
        };

        $this->expectException(InvalidUriException::class);
        $this->expectExceptionMessage('An error occurred while converting an URI of type');

        $this->factory->createUriFromString($stringable);
    }

    public function testCreateUriFromStringWithEmptyString(): void
    {
        $uri = $this->factory->createUriFromString('');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithOnlyFragment(): void
    {
        $uri = $this->factory->createUriFromString('#fragment');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertSame('fragment', $uri->fragment);
    }

    public function testCreateUriFromStringWithOnlyQuery(): void
    {
        $uri = $this->factory->createUriFromString('?key=value');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('', $uri->path->toString());
        self::assertSame('key=value', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithMalformedAuthority(): void
    {
        $uri = $this->factory->createUriFromString('http://user@:8080/path');

        // parse_url returns false for malformed authority, so all components are reset to defaults
        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithInvalidPort(): void
    {
        $uri = $this->factory->createUriFromString('http://example.com:99999/path');

        self::assertNull($uri->scheme);
        self::assertNull($uri->authority);
        self::assertSame('', $uri->path->toString());
        self::assertSame('', $uri->query->toString());
        self::assertNull($uri->fragment);
    }

    public function testCreateUriFromStringWithUriInterfaceReturnsClone(): void
    {
        $pathFactory = new UriPathFactory();
        $queryFactory = new UriQueryFactory();
        $schemeFactory = new UriSchemeFactory();

        $originalUri = new Uri(
            path: $pathFactory->createPathFromString('/test/path'), query: $queryFactory->createQueryFromString(
                'param=value',
            ), scheme: $schemeFactory->createSchemeFromString('https'), authority: null, fragment: 'test-fragment',
        );

        $clonedUri = $this->factory->createUriFromString($originalUri);

        self::assertNotSame($originalUri, $clonedUri);
        self::assertSame('/test/path', $clonedUri->path->toString());
        self::assertSame('param=value', $clonedUri->query->toString());
        self::assertSame('https', $clonedUri->scheme?->toString());
        self::assertSame('test-fragment', $clonedUri->fragment);
    }

    public function testCreateUriFromStringWithStringableObject(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'https://example.com/path?param=value#fragment';
            }
        };

        $uri = $this->factory->createUriFromString($stringable);

        self::assertSame('https', $uri->scheme?->toString());
        self::assertSame('example.com', $uri->authority?->host);
        self::assertSame('/path', $uri->path->toString());
        self::assertSame('param=value', $uri->query->toString());
        self::assertSame('fragment', $uri->fragment);
    }

    public function testCreateUriFromStringWithComponentExceptionIsWrapped(): void
    {
        $schemeFactory = new class implements UriSchemeFactoryInterface {
            public function createSchemeFromString(\Stringable|string $scheme): SchemeInterface
            {
                throw new InvalidUriSchemeComponentException('Test exception');
            }
        };

        $factory = new UriFactory(schemes: $schemeFactory);

        $this->expectException(InvalidUriException::class);
        $this->expectExceptionMessage('Test exception');

        $factory->createUriFromString('https://example.com');
    }
}
