<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http;

use Boson\Http\HeadersMap;
use Boson\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/runtime')]
final class HeadersMapTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $headers = new HeadersMap();

        self::assertCount(0, $headers);
        self::assertFalse($headers->has('Content-Type'));
    }

    public function testCreateFromArray(): void
    {
        $headers = HeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
            'X-Custom-Header' => ['value1', 'value2'],
        ]);

        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('x-custom-header'));
        self::assertSame('application/json', $headers->first('Content-Type'));
        self::assertSame(['value1', 'value2'], $headers->all('X-Custom-Header'));
    }

    public function testCreateFromHeadersMap(): void
    {
        $original = HeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
        ]);

        $headers = HeadersMap::createFromHeaders($original);

        self::assertTrue($headers->has('content-type'));
        self::assertSame('application/json', $headers->first('Content-Type'));
    }

    public function testFirstHeader(): void
    {
        $headers = HeadersMap::createFromIterable([
            'X-Custom' => ['value1', 'value2'],
        ]);

        self::assertSame('value1', $headers->first('X-Custom'));
        self::assertNull($headers->first('Non-Existent'));
        self::assertSame('default', $headers->first('Non-Existent', 'default'));
    }

    public function testAllHeaders(): void
    {
        $headers = HeadersMap::createFromIterable([
            'X-Custom' => ['value1', 'value2'],
        ]);

        self::assertSame(['value1', 'value2'], $headers->all('X-Custom'));
        self::assertSame([], $headers->all('Non-Existent'));
    }

    public function testContainsHeader(): void
    {
        $headers = HeadersMap::createFromIterable([
            'X-Custom' => ['value1', 'value2'],
        ]);

        self::assertTrue($headers->contains('X-Custom', 'value1'));
        self::assertTrue($headers->contains('x-custom', 'value2'));
        self::assertFalse($headers->contains('X-Custom', 'value3'));
        self::assertFalse($headers->contains('Non-Existent', 'value'));
    }

    public function testHeadersIteration(): void
    {
        $expected = [
            'content-type' => 'application/json',
            'x-custom' => 'value1',
            'x-custom' => 'value2',
        ];

        $headers = HeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
            'X-Custom' => ['value1', 'value2'],
        ]);

        $actual = [];
        foreach ($headers as $name => $value) {
            $actual[$name] = $value;
        }

        self::assertSame($expected, $actual);
    }

    public function testHeadersCount(): void
    {
        $headers = HeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
            'X-Custom' => ['value1', 'value2'],
        ]);

        self::assertCount(2, $headers);
    }

    public function testHeaderNameFormatting(): void
    {
        $headers = HeadersMap::createFromIterable([
            'CONTENT-TYPE' => 'application/json',
            'X-Custom-Header' => 'value',
        ]);

        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('x-custom-header'));
        self::assertTrue($headers->has('Content-Type'));
        self::assertTrue($headers->has('X-CUSTOM-HEADER'));
    }
}
