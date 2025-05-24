<?php

declare(strict_types=1);

namespace Boson\Component\Http\Tests;

use Boson\Component\Http\Request;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/http')]
final class RequestTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $request = new Request();

        self::assertSame('GET', $request->method);
        self::assertSame('about:blank', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomMethod(): void
    {
        $request = new Request('POST');

        self::assertSame('POST', $request->method);
        self::assertSame('about:blank', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomUrl(): void
    {
        $request = new Request(
            method: 'GET',
            url: 'https://example.com/api',
        );

        self::assertSame('GET', $request->method);
        self::assertSame('https://example.com/api', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomHeaders(): void
    {
        $request = new Request(
            method: 'GET',
            url: '/',
            headers: [
                'Content-Type' => 'application/json',
                'X-Custom' => 'value',
            ],
        );

        self::assertSame('GET', $request->method);
        self::assertSame('/', $request->url);
        self::assertCount(2, $request->headers);
        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertTrue($request->headers->has('x-custom'));
        self::assertSame('value', $request->headers->first('x-custom'));
        self::assertSame('', $request->body);
    }

    public function testCreateWithCustomBody(): void
    {
        $request = new Request(
            method: 'POST',
            url: '/',
            headers: [],
            body: '{"key": "value"}',
        );

        self::assertSame('POST', $request->method);
        self::assertSame('/', $request->url);
        self::assertCount(0, $request->headers);
        self::assertSame('{"key": "value"}', $request->body);
    }

    public function testCreateFromRequest(): void
    {
        $original = new Request(
            method: 'POST',
            url: 'https://example.com/api',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: '{"key": "value"}',
        );

        $request = Request::createFromRequest($original);

        self::assertSame('POST', $request->method);
        self::assertSame('https://example.com/api', $request->url);
        self::assertCount(1, $request->headers);
        self::assertTrue($request->headers->has('content-type'));
        self::assertSame('application/json', $request->headers->first('content-type'));
        self::assertSame('{"key": "value"}', $request->body);
    }

    public function testCreateFromRequestWithCloning(): void
    {
        $original = new Request(
            method: 'POST',
            url: 'https://example.com/api',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: '{"key": "value"}',
        );

        $request = Request::createFromRequest($original);

        self::assertNotSame($original->headers, $request->headers);
    }

    public function testMethodCaseInsensitivity(): void
    {
        $request = new Request('post');

        self::assertSame('POST', $request->method);
    }
}
