<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Http;

use Boson\Http\Method;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[Group('serafim/boson')]
final class MethodTest extends HttpTestCase
{
    /**
     * @return non-empty-array<non-empty-string, list<mixed>>
     */
    public static function standardMethodsProvider(): array
    {
        return [
            'GET method' => ['GET', true, true],
            'HEAD method' => ['HEAD', true, true],
            'OPTIONS method' => ['OPTIONS', true, true],
            'TRACE method' => ['TRACE', true, true],
            'PUT method' => ['PUT', true, false],
            'DELETE method' => ['DELETE', true, false],
            'POST method' => ['POST', false, false],
            'PATCH method' => ['PATCH', false, false],
            'CONNECT method' => ['CONNECT', false, false],
        ];
    }

    #[DataProvider('standardMethodsProvider')]
    public function testStandardMethods(string $name, bool $isIdempotent, bool $isSafe): void
    {
        $method = Method::from($name);

        self::assertSame($name, $method->name);
        self::assertSame($isIdempotent, $method->isIdempotent);
        self::assertSame($isSafe, $method->isSafe);
        self::assertSame($name, (string) $method);
    }

    public function testFromWithValidMethod(): void
    {
        $method = Method::from('GET');

        self::assertSame('GET', $method->name);
        self::assertTrue($method->isIdempotent);
        self::assertTrue($method->isSafe);
    }

    public function testFromWithInvalidMethod(): void
    {
        $this->expectException(\ValueError::class);
        Method::from('INVALID');
    }

    public function testFromWithInvalidCase(): void
    {
        $this->expectException(\ValueError::class);
        Method::from('get');
    }

    public function testTryFromWithValidMethod(): void
    {
        $method = Method::tryFrom('GET');

        self::assertNotNull($method);
        self::assertSame('GET', $method->name);
        self::assertTrue($method->isIdempotent);
        self::assertTrue($method->isSafe);
    }

    public function testTryFromWithInvalidMethod(): void
    {
        $method = Method::tryFrom('INVALID');

        self::assertNull($method);
    }

    public function testTryFromWithInvalidCase(): void
    {
        $method = Method::tryFrom('get');

        self::assertNull($method);
    }

    public function testCustomMethod(): void
    {
        $method = new Method('CUSTOM');

        self::assertSame('CUSTOM', $method->name);
        self::assertFalse($method->isIdempotent);
        self::assertFalse($method->isSafe);
    }

    public function testCustomMethodWithProperties(): void
    {
        $method = new Method('CUSTOM', isIdempotent: true, isSafe: true);

        self::assertSame('CUSTOM', $method->name);
        self::assertTrue($method->isIdempotent);
        self::assertTrue($method->isSafe);
    }
}
