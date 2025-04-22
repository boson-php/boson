<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http\Uri;

use Boson\Http\Uri\Scheme;
use Boson\Http\Uri\SchemeInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class SchemeTest extends UriTestCase
{
    public function testCreateSchemeFromString(): void
    {
        $scheme = Scheme::tryFrom('http');
        self::assertInstanceOf(SchemeInterface::class, $scheme);
        self::assertSame('http', $scheme->name);
    }

    public function testCreateSchemeFromStringWithUppercase(): void
    {
        $scheme = Scheme::tryFrom(\strtolower('HTTP'));
        self::assertInstanceOf(SchemeInterface::class, $scheme);
        self::assertSame('http', $scheme->name);
    }

    public function testCreateSchemeFromStringWithInvalidValue(): void
    {
        $scheme = Scheme::tryFrom('invalid-scheme');
        self::assertNull($scheme);
    }

    public function testFromWithInvalidValue(): void
    {
        self::expectException(\ValueError::class);
        Scheme::from('invalid-scheme');
    }

    public function testGetAllStandardSchemes(): void
    {
        $schemes = Scheme::cases();
        self::assertNotEmpty($schemes);

        $expected = ['http', 'https', 'data', 'file', 'ftp', 'gopher', 'ws', 'wss'];

        /** @var list<non-empty-string> $actual */
        $actual = [];
        foreach ($schemes as $scheme) {
            $actual[] = $scheme->name;
        }

        \sort($expected);
        \sort($actual);

        self::assertSame($expected, $actual);
    }

    public function testSchemeToString(): void
    {
        $scheme = Scheme::from('http');
        self::assertSame('http', (string)$scheme);
    }

    public function testStandardSchemePorts(): void
    {
        self::assertSame(80, Scheme::Http->port);
        self::assertSame(443, Scheme::Https->port);
        self::assertSame(21, Scheme::Ftp->port);
    }
}
