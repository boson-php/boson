<?php

declare(strict_types=1);

namespace Boson\Component\Http\Tests;

use Boson\Component\Http\MutableHeadersMap;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http')]
final class MutableHeadersMapTest extends TestCase
{
    public function testSetHeader(): void
    {
        $headers = new MutableHeadersMap();

        $headers->set('Content-Type', 'application/json');

        self::assertTrue($headers->has('content-type'));
        self::assertSame('application/json', $headers->first('Content-Type'));
        self::assertCount(1, $headers);
    }

    public function testSetHeaderOverwritesExisting(): void
    {
        $headers = new MutableHeadersMap();

        $headers->set('Content-Type', 'text/plain');
        $headers->set('Content-Type', 'application/json');

        self::assertTrue($headers->has('content-type'));
        self::assertSame('application/json', $headers->first('Content-Type'));
        self::assertCount(1, $headers);
    }

    public function testAddHeader(): void
    {
        $headers = new MutableHeadersMap();

        $headers->add('X-Custom', 'value1');
        $headers->add('X-Custom', 'value2');

        self::assertTrue($headers->has('x-custom'));
        self::assertSame(['value1', 'value2'], $headers->all('X-Custom'));
        self::assertCount(1, $headers);
    }

    public function testRemoveHeader(): void
    {
        $headers = new MutableHeadersMap();

        $headers->set('Content-Type', 'application/json');
        $headers->set('X-Custom', 'value');

        $headers->remove('content-type');

        self::assertFalse($headers->has('Content-Type'));
        self::assertTrue($headers->has('X-Custom'));
        self::assertCount(1, $headers);
    }

    public function testRemoveAllHeaders(): void
    {
        $headers = new MutableHeadersMap();

        $headers->set('Content-Type', 'application/json');
        $headers->set('X-Custom', 'value');

        $headers->removeAll();

        self::assertFalse($headers->has('Content-Type'));
        self::assertFalse($headers->has('X-Custom'));
        self::assertCount(0, $headers);
    }

    public function testCreateFromIterable(): void
    {
        $headers = MutableHeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
            'X-Custom-Header' => ['value1', 'value2'],
        ]);

        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('x-custom-header'));
        self::assertSame('application/json', $headers->first('Content-Type'));
        self::assertSame(['value1', 'value2'], $headers->all('X-Custom-Header'));
        self::assertCount(2, $headers);
    }

    public function testCreateFromHeadersMap(): void
    {
        $original = MutableHeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
        ]);

        $headers = MutableHeadersMap::createFromHeaders($original);

        self::assertTrue($headers->has('content-type'));
        self::assertSame('application/json', $headers->first('Content-Type'));
        self::assertCount(1, $headers);
    }

    public function testHeaderNameFormatting(): void
    {
        $headers = new MutableHeadersMap();

        $headers->set('CONTENT-TYPE', 'application/json');
        $headers->set('X-Custom-Header', 'value');

        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('x-custom-header'));
        self::assertTrue($headers->has('Content-Type'));
        self::assertTrue($headers->has('X-CUSTOM-HEADER'));
    }

    public function testHeadersIteration(): void
    {
        $expected = [
            'content-type' => 'application/json',
            'x-custom' => 'value2',
        ];

        $headers = MutableHeadersMap::createFromIterable([
            'Content-Type' => 'application/json',
            'X-Custom' => ['value1', 'value2'],
        ]);

        $actual = [];
        foreach ($headers as $name => $value) {
            $actual[$name] = $value;
        }

        self::assertSame($expected, $actual);
    }
}
