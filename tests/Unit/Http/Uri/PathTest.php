<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http\Uri;

use Boson\Http\Uri\Path;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class PathTest extends UriTestCase
{
    public function testCreateEmptyPath(): void
    {
        $path = new Path();

        self::assertSame('/', (string)$path);
        self::assertSame(0, $path->count());
    }

    public function testCreatePathWithSingleSegment(): void
    {
        $path = new Path(['segment']);

        self::assertSame('/segment', (string)$path);
        self::assertSame(1, $path->count());
    }

    public function testCreatePathWithMultipleSegments(): void
    {
        $path = new Path(['segment1', 'segment2', 'segment3']);

        self::assertSame('/segment1/segment2/segment3', (string)$path);
        self::assertSame(3, $path->count());
    }

    public function testCreatePathWithSpecialCharacters(): void
    {
        $path = new Path(['segment with spaces', 'segment/with/slashes', 'segment&with&ampersands']);

        self::assertSame(
            '/segment+with+spaces/segment%2Fwith%2Fslashes/segment%26with%26ampersands',
            (string)$path
        );
    }

    public function testGetIterator(): void
    {
        $segments = ['segment1', 'segment2', 'segment3'];
        $path = new Path($segments);
        $result = [];

        foreach ($path as $segment) {
            $result[] = $segment;
        }

        self::assertSame($segments, $result);
    }

    public function testCreatePathFromIterable(): void
    {
        $segments = (static function (): \Generator {
            yield 'segment1';
            yield 'segment2';
            yield 'segment3';
        })();

        $path = new Path($segments);

        self::assertSame('/segment1/segment2/segment3', (string)$path);
        self::assertSame(3, $path->count());
    }

    public function testCreatePathWithEmptySegments(): void
    {
        $segments = ['segment1', 'segment2', 'segment3'];
        $path = new Path($segments);

        self::assertSame('/segment1/segment2/segment3', (string)$path);
        self::assertSame(3, $path->count());

        foreach ($path as $segment) {
            self::assertNotEmpty($segment);
        }
    }
}
