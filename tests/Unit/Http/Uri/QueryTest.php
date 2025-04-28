<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http\Uri;

use Boson\Http\Uri\Query;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class QueryTest extends UriTestCase
{
    public function testCreateEmptyQuery(): void
    {
        $query = new Query();

        self::assertSame('', (string)$query);
        self::assertSame(0, $query->count());
    }

    public function testCreateQueryWithSingleComponent(): void
    {
        $query = new Query(['key' => 'value']);

        self::assertSame('key=value', (string)$query);
        self::assertSame(1, $query->count());
    }

    public function testCreateQueryWithMultipleComponents(): void
    {
        $query = new Query([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertSame('key1=value1&key2=value2', (string)$query);
        self::assertSame(2, $query->count());
    }

    public function testCreateQueryWithDuplicateKeys(): void
    {
        $components = (static function (): \Generator {
            yield 'key' => 'value1';
            yield 'key' => 'value2';
        })();

        $query = new Query($components);

        self::assertSame('value2', $query->get('key'));
        self::assertSame('key=value2', (string) $query);
    }

    public function testCreateQueryWithSpecialCharacters(): void
    {
        $query = new Query([
            'key with spaces' => 'value with spaces',
            'key&with&ampersands' => 'value&with&ampersands',
        ]);

        self::assertSame(
            'key+with+spaces=value+with+spaces&key%26with%26ampersands=value%26with%26ampersands',
            (string)$query
        );
    }

    public function testOffsetExists(): void
    {
        $query = new Query(['key' => 'value']);

        self::assertTrue($query->has('key'));
        self::assertFalse($query->has('non-existent'));
    }

    public function testGetIterator(): void
    {
        $components = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $query = new Query($components);
        $result = [];

        foreach ($query as $key => $value) {
            $result[$key] = $value;
        }

        self::assertSame($components, $result);
    }

    public function testCreateQueryWithEmptyValues(): void
    {
        $query = new Query([
            'key1' => '',
            'key2' => '0',
        ]);

        self::assertSame('', $query->get('key1'));
        self::assertSame('0', $query->get('key2'));
        self::assertSame('key1=&key2=0', (string) $query);
    }
}
